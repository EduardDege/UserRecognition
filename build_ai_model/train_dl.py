# -*- coding: utf-8 -*-
import os
import sys
import csv
import time
import json
import datetime
import pickle as pkl
import warnings
timestamp = str(int(time.time()))
outdir = os.path.abspath(os.path.join(os.path.curdir, "runs", timestamp))
if not os.path.exists(outdir):
    os.makedirs(outdir)

warnings.filterwarnings("ignore", category=FutureWarning)
import tensorflow as tf
from tensorflow.contrib import learn
from DataLoader import DataLoader
data_loader = DataLoader()
from classifier import cnn_clf
from configuration import cnn_config
from sklearn.metrics import precision_recall_fscore_support
model = "clstm"
from matplotlib import colors
from matplotlib import pyplot as plt
print("hello")

# Train

# cost = tf.nn

with tf.Graph().as_default():
    with tf.compat.v1.Session() as sess:
        print("train")

        classifier = cnn_clf(cnn_config())

        global_step = tf.Variable(0, name='global_step', trainable=False)
        start_learning_rate = 1e-3
        learning_rate = tf.compat.v1.train.exponential_decay(start_learning_rate, global_step, 1000, 1, staircase=True)

        optimizer = tf.compat.v1.train.AdamOptimizer(learning_rate)
        grads_and_vars = optimizer.compute_gradients(classifier.cost)
        train_op = optimizer.apply_gradients(grads_and_vars, global_step=global_step)

        loss_summary = tf.compat.v1.summary.scalar("Loss", classifier.cost)
        accuracy_summary = tf.compat.v1.summary.scalar("Accuracy", classifier.accuracy)

        train_summary_op = tf.compat.v1.summary.merge_all()
        train_summary_dir = os.path.join(outdir, 'summaries', 'train')
        train_summary_writer = tf.compat.v1.summary.FileWriter(train_summary_dir, sess.graph)

        test_summary_op = tf.compat.v1.summary.merge_all()
        test_summary_dir = os.path.join(outdir, 'summaries', 'test')
        test_summary_writer = tf.compat.v1.summary.FileWriter(test_summary_dir, sess.graph)

        saver = tf.compat.v1.train.Saver(max_to_keep=cnn_config().num_checkpoint)

        sess.run(tf.compat.v1.global_variables_initializer())


        def run_step(model, input_x, input_y, is_training=True):
            """Run one step of the training process."""
            # input_x, input_y, sequence_length = input_data

            fetches = {'step': global_step,
                       'cost': classifier.cost,
                       'accuracy': classifier.accuracy,
                       'predictions': classifier.predictions,
                       'learning_rate': learning_rate}
            feed_dict = {classifier.input_x: input_x,
                         classifier.input_y: input_y}

            pass

            if is_training:
                fetches['train_op'] = train_op
                fetches['summaries'] = train_summary_op
                feed_dict[classifier.keep_prob] = cnn_config().keep_prob
            else:
                fetches['summaries'] = test_summary_op
                feed_dict[classifier.keep_prob] = 1.0

            vars = sess.run(fetches, feed_dict)
            step = vars['step']
            cost = vars['cost']
            accuracy = vars['accuracy']
            predictions = vars['predictions']
            summaries = vars['summaries']

            precision, recall, f1, _ = precision_recall_fscore_support(input_y, predictions, average='binary')
            # Write summaries to file
            if is_training:
                train_summary_writer.add_summary(summaries, step)
            else:
                test_summary_writer.add_summary(summaries, step)

            time_str = datetime.datetime.now().isoformat()
            print("{}: step: {}, loss: {:g}, accuracy: {:g}, precision: {:g}, recall: {:g}, f1: {:g}".format(time_str,
                                                                                                             step, cost,
                                                                                                             accuracy,
                                                                                                             precision,
                                                                                                             recall,
                                                                                                             f1))

            return accuracy

        print("Start training ...")

        for i in range(cnn_config().num_epochs):
            for j in range(data_loader.num_batches):
                input_x, input_y = data_loader.next_batch()
                run_step(model, input_x, input_y, is_training=True)
                current_step = tf.train.global_step(sess, global_step)
                if current_step % cnn_config.evaluate_every_steps == 0:
                    print('\nTest')
                    input_x, input_y = data_loader.get_test_data()
                    run_step(model, input_x, input_y, is_training=False)
                    print('')
                if current_step % cnn_config.save_every_steps == 0:
                    save_path = saver.save(sess, os.path.join(outdir, 'model/clf'), current_step)

        fig = plt.figure(figsize=(5, 3.75))
        ax = fig.add_subplot(111)

        ax.scatter

        print('\nAll the files have been saved to {}\n'.format(outdir))

