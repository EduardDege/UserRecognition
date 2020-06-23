# %% [code]
# This Python 3 environment comes with many helpful analytics libraries installed
# It is defined by the kaggle/python Docker image: https://github.com/kaggle/docker-python
# For example, here's several helpful packages to load

import numpy as np  # linear algebra
import pandas as pd  # data processing, CSV file I/O (e.g. pd.read_csv)

# Input data files are available in the read-only "../input/" directory
# For example, running this (by clicking run or pressing Shift+Enter) will list all files under the input directory

import os

for dirname, _, filenames in os.walk('.'):
    for filename in filenames:
        print(os.path.join(dirname, filename))

# You can write up to 5GB to the current directory (/kaggle/working/) that gets preserved as output when you create a version using "Save & Run All"
# You can also write temporary files to /kaggle/temp/, but they won't be saved outside of the current session

# %% [code]
# This Python 3 environment comes with many helpful analytics libraries installed
# It is defined by the kaggle/python docker image: https://github.com/kaggle/docker-python
# For example, here's several helpful packages to load in

import numpy as np  # linear algebra
import pandas as pd  # data processing, CSV file I/O (e.g. pd.read_csv)
from sklearn import feature_extraction, linear_model, model_selection, preprocessing

# Input data files are available in the "../input/" directory.
# For example, running this (by clicking run or pressing Shift+Enter) will list all files under the input directory

import os
# Any results you write to the current directory are saved as output.
# tensorflow hub
import tensorflow_hub as hub
# tensor flow module
import tensorflow as tf
import tensorflow_probability as tfp

# matplotlib
from matplotlib import colors
from matplotlib import pyplot as plt

# word vectorizor
# first converts the text into a matrix of word counts
# then transforms these counts by normalizing them based on the term frequency
from sklearn.feature_extraction.text import TfidfVectorizer

# used to create word encoders
from sklearn import preprocessing


class TFNaiveBayesClassifier:
    dist = None

    # X is the matrix containing the vectors for each sentence
    # y is the list target values in the same order as the X matrix

    def fit(self, X, y):
        unique_y = np.unique(y)  # unique target values: 0,1
        print(unique_y)
        # `points_by_class` is a numpy array the size of
        # the number of unique targets.
        # in each item of the list is another list that contains the vector
        # of each sentence from the same target value
        points_by_class = np.asarray([np.asarray(
            [np.asarray(
                X.iloc[x, :]) for x in range(0, len(y)) if y[x] == c]) for c in unique_y])
        mean_list = []
        var_list = []
        for i in range(0, len(points_by_class)):
            mean_var, var_var = tf.nn.moments(tf.constant(points_by_class[i]), axes=[0])
            mean_list.append(mean_var)
            var_list.append(var_var)
        mean = tf.stack(mean_list, 0)
        var = tf.stack(var_list, 0)
        # Create a 3x2 univariate normal distribution with the
        # known mean and variance
        self.dist = tfp.distributions.Normal(loc=mean, scale=tf.sqrt(var))

    def predict(self, X):
        assert self.dist is not None
        nb_classes, nb_features = map(int, self.dist.scale.shape)

        # uniform priors
        priors = np.log(np.array([1. / nb_classes] * nb_classes))

        # Conditional probabilities log P(x|c)
        # (nb_samples, nb_classes, nb_features)
        all_log_probs = self.dist.log_prob(
            tf.reshape(
                tf.tile(X, [1, nb_classes]), [-1, nb_classes, nb_features]))
        # (nb_samples, nb_classes)
        cond_probs = tf.reduce_sum(all_log_probs, axis=2)

        # posterior log probability, log P(c) + log P(x|c)
        joint_likelihood = tf.add(priors, cond_probs)

        # normalize to get (log)-probabilities
        norm_factor = tf.reduce_logsumexp(
            joint_likelihood, axis=1, keepdims=True)
        log_prob = joint_likelihood - norm_factor
        # exp to get the actual probabilities
        return tf.exp(log_prob)


def initModel(X_train_matrix, X_test_matrix, y_train, train_df):
    # Here we initialize our naive bayes model and fit it using the training data
    tf_nb = TFNaiveBayesClassifier()
    tf_nb.fit(pd.DataFrame(X_train_matrix), y_train)
    # predict probability of each target values in the test set
    y_pred = tf_nb.predict(X_test_matrix)
    # Create a dataframe containing the probability of each target given the text in each tweet.
    predProbGivenText_df = pd.DataFrame(y_pred.numpy())
    predProbGivenText_df.head()
    uniq_keywords = train_df["keyword"].unique()[1:]
    print(len(uniq_keywords))
    print(uniq_keywords)


def getProbality(train_df):
    uniq_keywords = train_df["keyword"].unique()[1:]
    kword_resArr = []
    print(len(uniq_keywords))
    for kword in uniq_keywords:
        kword_df = train_df.loc[train_df["keyword"] == kword, :]
        total_kword = float(len(kword_df))
        target0_n = float(len(kword_df.loc[kword_df["target"] == 0, :]))
        target1_n = float(len(kword_df.loc[kword_df["target"] == 1, :]))
        kword_prob_df = pd.DataFrame({'keyword': [kword],
                                      "keywordPred0": [target0_n / total_kword],
                                      "keywordPred1": [target1_n / total_kword]})
        kword_resArr.append(kword_prob_df)
    predProbGivenKeyWord_df = pd.concat(kword_resArr)
    predProbGivenKeyWord_df.head()
    print(predProbGivenKeyWord_df.head())


def main():
    train_df = pd.read_csv("train.csv")
    test_df = pd.read_csv("train.csv")
    train_df.groupby("target")["id"].nunique()
    # print(len(test_df.loc[test_df["location"].notnull(), "location"]))
    # print(len(test_df.loc[test_df["location"].notnull(), "location"].unique()))
    # print(len(test_df.loc[test_df["keyword"].notnull(), "keyword"]))
    # print(len(test_df.loc[test_df["keyword"].notnull(), "keyword"].unique()))
    # Tweet Text: Sentence Embedding
    embed = hub.load("https://tfhub.dev/google/universal-sentence-encoder/3")
    # print(embed)
    X_train_embeddings = embed(train_df["text"].values)
    X_test_embeddings = embed(test_df["text"].values)
    # Tensor Flow Naiver Bayes
    X_train_matrix = X_train_embeddings['outputs'].numpy()
    X_test_matrix = X_test_embeddings['outputs'].numpy()
    y_train = tf.constant(train_df["target"])
    initModel(X_train_matrix, X_test_matrix, y_train, train_df)
    getProbality(train_df)


if __name__ == '__main__':
    main()

# %% [code]
