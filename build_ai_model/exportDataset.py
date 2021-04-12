"""
 This is the Tool to analyse a Use Behavior on a Wordpress-Website
 The User can be blocker, if the Machine Learning Algo evaluate the use ask risk
"""
import sys
import time
from mysql.connector import Error
from mysql.connector import pooling
import mariadb
import json
from pathlib import Path
from sklearn.preprocessing import MinMaxScaler
from functools import reduce
import numpy as np
import pandas as pd
import scipy
from pandas import DataFrame
from sklearn import preprocessing
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import Normalizer
from sklearn.neighbors import KNeighborsClassifier
import random
from sklearn.ensemble import RandomForestRegressor
import warnings
warnings.filterwarnings("ignore", category=FutureWarning)
import tensorflow as tf
from tensorflow import keras
from tensorflow.keras import layers
from sklearn.metrics import mean_squared_error
from tensorflow.keras.layers import *
from tensorflow.keras.models import *


def rescale_data():
    
    url = "https://raw.githubusercontent.com/jbrownlee/Datasets/master/pima-indians-diabetes.csv"
    names = ['preg', 'plas', 'pres', 'skin', 'test', 'mass', 'pedi', 'age', 'class']
    dataframe = pd.read_csv(url, names=names)
    array = dataframe.values
    # separate array into input and output components
    X = array[:,0:8]
    Y = array[:,8]
    print(X)
    #print(Y)
    scaler = Normalizer().fit(X)
    normalizedX = scaler.transform(X)
    # summarize transformed data
    np.set_printoptions(precision=3)
    print(normalizedX[0:5,:])
    print('Rows: %d' % X.shape[0])
    #print('Cols: %d' % Y.shape[1])
    

def exportColNames(cursor):
    columns = []
    for i in cursor.description:
        columns.append(i[0])
    return columns


def get_dataset():
    pool = mariadb.ConnectionPool(
        pool_name='pool1',
        pool_size=3,
        pool_reset_connection=False,
        host='194.94.127.112',
        user='ifis1',
        password='b0QrDr8ShG#e@iMWDwGKlgw3',
        database='WordPress',
    )
    try:
        # These would normally be allocated in separate threads:
        conn1 = pool.get_connection()
        conn2 = pool.get_connection()
        conn3 = pool.get_connection()
        # Attempting to fetch a 4th connection would throw an exception
        # given the pool_size == 3 option above.

        # get session_logs table
        cursor = conn1.cursor()
        cursor.execute('SELECT session_id, ip_address, session_date, countrycode, state, user_agent, '
                       'platform, browser, subpage, ip_blacklisten, download_link, time_spent '
                       'FROM wp_ifiS_02session_logs')

        session_logs = DataFrame(cursor, columns=exportColNames(cursor))

        # print(session_logs)

        conn1.close()

        # get usermovement table
        cursor = conn2.cursor()
        cursor.execute('SELECT session_id, type, button, click_positions, start_movement, end_movement, subpage '
                       'FROM wp_ifiS_02usermovement')

        usermovement = DataFrame(cursor, columns=exportColNames(cursor))

        # print(usermovement)

        conn2.close()

        # get user_login_data table
        cursor = conn3.cursor()
        cursor.execute('SELECT session_id, user_id, login_attempt, login_attempt_date, logout_date, duration '
                       'FROM wp_ifiS_02user_login_data')

        user_login_data = DataFrame(cursor, columns=exportColNames(cursor))

        # print(user_login_data)

        conn3.close()

        data_frames = [session_logs, user_login_data, usermovement]

        merged = reduce(lambda left, right: pd.merge(left, right, on=['session_id'], how='outer'), data_frames)
        columns = list(merged)

        # print(merged)
        print('Rows: %d' % merged.shape[0])
        print('Cols: %d' % merged.shape[1]) 

        return merged, columns

    except mariadb.Error as e:
        print(f"Error:{e}")


def get_preprocessed_dataset():
    # start_time = time.time()
    data, cols = get_dataset()
    merged = data
    cat_cols = cols
    # print(merged)
    for var in cat_cols:
        number = preprocessing.LabelEncoder()
        merged[var] = number.fit_transform(merged[var].astype('str'))

    # print(type(merged))
    '''np.savetxt("data2.csv", merged, delimiter=",")'''

    df_values = merged.values
    # print(df_values)
    min_max_scaler = preprocessing.MinMaxScaler()
    merged_scaled = min_max_scaler.fit_transform(df_values)
    data = pd.DataFrame(merged_scaled)
    x_data, y_data = np.array_split(data, 2)

    # print(x_data)
    # print(y_data)

    if len(x_data) != len(y_data):
        # X_train.drop(X_train.tail(1).index, inplace = True)
        x_data = x_data.head(-1)

    X_train, X_test, y_train, y_test = train_test_split(x_data, y_data, test_size=0.3, random_state=42)

    # ohe = preprocessing.OneHotEncoder(handle_unknown="ignore")

    # print(time.time() - start_time)
    # print(X_train)
    return X_train, X_test, y_train, y_test


def generate_model():
    # 0 = X_train, 1 = X_test, 2 = y_train, 3 = y_test
    data = get_preprocessed_dataset()
    start_xy = np.array(data)
    start_xy = start_xy.reshape(*start_xy.shape, 1)

    #print(start_xy)
    # modelbuilding with random forest
    '''random.seed(42)
    rf = RandomForestRegressor(n_estimators=10)
    rf.fit(X_train, y_train)'''

    # modelbuilding using keras
    # 15 input neurons, 100 hidden layer1 neurons, 50 hidden layer2 neurons, 15 output neuron
    # input_shape = (8959, 23, 23, 1)
    # input_shape = (8959, 23, 23, 1)
    # model.add(layers.Dense(100, activation="relu", input_shape=start_xy.shape[1:3]))
    model = tf.keras.Sequential()
    model.add(layers.Dense(100, activation="relu", input_shape = (8959, 23, 23, 1)))
    model.add(layers.Dense(50, activation="relu"))
    model.add(layers.Dense(25))
    model.summary()

    # model.compile(loss="mean_squared_error", optimizer="adam", metrics=["mean_squared_error"])
    model.compile(loss='binary_crossentropy', optimizer='adam', metrics=['accuracy'])


    # model.fit(data[0], data[2], epochs=10)

    # model.fit(data[0], data[2], epochs=10, validation_data=(data[1], data[3]))

    train_data = tf.data.Dataset.from_tensor_slices((data[0], data[2]))
    # print(train_data)
    valid_data = tf.data.Dataset.from_tensor_slices((data[1], data[3]))

    # fit the keras model on the dataset -> Model Convergence
    model.fit(train_data, epochs=10, validation_data=valid_data)
    #sys.stdout.flush()
    #model.save_weights('./models/convnet_weights.h5')
    pred = model.predict(data[1])
    score = np.sqrt(mean_squared_error(data[3], pred))
    print(score)

def create_model():
    # 0 = X_train, 1 = X_test, 2 = y_train, 3 = y_test
    X_train, X_test, y_train, y_test = get_preprocessed_dataset()
    print(X_train)
    x = np.random.randint(0,X_test, X_train)
    y = np.eye((y_train))[np.random.randint(0,X_test,X_train)]
    xr = x.reshape((-1,1))


    # print("x.shape: {}\nxr.shape:{}\ny.shape: {}".format(x.shape, xr.shape, y.shape))


    #model = Sequential()
    #model.add(Embedding(2557, 64, input_length=1, embeddings_initializer='glorot_uniform'))
    #model.add(Reshape((64,)))
    #model.add(Dense(512, activation='sigmoid'))
    #model.add(Dense(2557, activation='softmax'))

    #model.compile(optimizer='adam', loss='categorical_crossentropy', metrics=['accuracy'])
    #model.summary()

    #history=model.fit(xr, y, epochs=20, batch_size=32, validation_split=3/9)

def main():
    # rescale_data()
    # get_dataset()
    #get_preprocessed_dataset()
    # generate_model()
    create_model()
    
if __name__ == '__main__':
    # execute only if run as a script
    start = time.time()
    main()
    end = time.time()
    hours, rem = divmod(end-start, 3600)
    minutes, seconds = divmod(rem, 60)
    print("{:0>2}:{:0>2}:{:05.2f}".format(int(hours),int(minutes),seconds))
