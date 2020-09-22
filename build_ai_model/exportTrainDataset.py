"""
 This is the Tool to analyse a Use Behavior on a Wordpress-Website
 The User can be blocker, if the Machine Learning Algo evaluate the use ask risk
"""


def dataImport():
    pool = mariadb.ConnectionPool(
        pool_name='pool1',
        pool_size=2,
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
        # Attempting to fetch a 4th connection would throw an exception
        # given the pool_size == 3 option above.

        cursor = conn1.cursor()
        cursor.execute('SELECT wp.id, wp.session_id, wp.ip_address, wp.login_attempt, '
                       'wp.countrycode, wp.user_id, wp.state FROM wp_ifiS_02session wp')

        result_session = cursor

        cursor = conn2.cursor()
        cursor.execute('SELECT ur.id, ur.user_id, ur.browser, ur.browser_version, ur.user_agent, ur.platform, '
                       'ur.login_date, ur.logout_date, ur.duration, ur.loginstatus, ur.subpage'
                       ' FROM wp_ifiS_02user_recognition ur')

        result_ur = cursor

        df_session = DataFrame(result_session,
                               columns=["id", "session_id", "ip_address", "login_attempt", "countrycode",
                                        "user_id", "state"])
        df_ur = DataFrame(result_ur, columns=["id", "user_id", "browser", "browser_version", "user_agent", "platform",
                                              "login_date", "logout_date", "duration",
                                              "loginstatus", "subpage"])

        conn1.close()
        conn2.close()

        merged = pd.merge(df_ur, df_session, how='inner', on="user_id", validate="many_to_many")
        frames = [df_session, df_ur]

        concat = pd.concat(frames)

        # merged = merged.dropna()

        # pd.set_option('display.max_columns', None)

        print(merged)

        # np.savetxt("data.csv", merged, delimiter=",")

        return merged

    except mariadb.Error as e:
        print(f"Error:{e}")


def prepareData():
    start_time = time.time()
    merged = dataImport()

    cat_cols = ["browser", "browser_version", "user_agent", "platform",
                "login_attempt", "login_date", "logout_date", "duration",
                "loginstatus", "subpage", "session_id", "ip_address", "countrycode",
                "user_id", "state"]

    for var in cat_cols:
        number = preprocessing.LabelEncoder()
        merged[var] = number.fit_transform(merged[var].astype('str'))

    # print(type(merged))
    '''np.savetxt("data2.csv", merged, delimiter=",")'''

    df_values = merged.values

    min_max_scaler = preprocessing.MinMaxScaler()
    merged_scaled = min_max_scaler.fit_transform(df_values)
    data = pd.DataFrame(merged_scaled)
    '''np.savetxt("data3.csv", data, delimiter=",")'''
    x_data, y_data = np.array_split(data, 2)

    '''print(x_data)
    print(y_data)'''

    if len(x_data) != len(y_data):
        # X_train.drop(X_train.tail(1).index, inplace = True)
        x_data = x_data.head(-1)

    X_train, X_test, y_train, y_test = train_test_split(x_data, y_data, test_size=0.3, random_state=42)

    # ohe = preprocessing.OneHotEncoder(handle_unknown="ignore")

    print(time.time() - start_time)

    return X_train, X_test, y_train, y_test


def modelBuilding():
    # 0 = X_train, 1 = X_test, 2 = y_train, 3 = y_test
    data = prepareData()
    # modelbuilding with random forest
    '''random.seed(42)
    rf = RandomForestRegressor(n_estimators=10)
    rf.fit(X_train, y_train)'''

    # modelbuilding using keras
    # 15 input neurons, 100 hidden layer1 neurons, 50 hidden layer2 neurons, 15 output neuron
    model = Sequential()
    model.add(Dense(100, input_dim=15, activation="relu"))
    model.add(Dense(50, activation="relu"))
    model.add(Dense(15))
    model.summary()

    model.compile(loss="mean_squared_error", optimizer="adam", metrics=["mean_squared_error"])

    # model.fit(data[0], data[2], epochs=10)

    model.fit(data[0], data[2], epochs=10, validation_data=(data[1], data[3]))

    pred = model.predict(data[1])
    score = np.sqrt(mean_squared_error(data[3], pred))
    print(score)


def exportDBUBAIFIS():
    start_time = time.time()
    connection = mysql.connector.connect(host='194.94.127.112',
                                         database='WordPress',
                                         user='ifis1',
                                         password='b0QrDr8ShG#e@iMWDwGKlgw3')
    try:
        if connection.is_connected():
            db_info = connection.get_server_info()
            print("Connected to MySQL Server version ", db_info)
            cursor = connection.cursor()
            cursor.execute("select database();")
            record = cursor.fetchone()
            print("You're connected to database: ", record)

            my_cursor = connection.cursor()

            # 190 sec
            '''my_cursor.execute('SELECT wp_ifiS_02session.session_id AS session_id, '
                              'wp_ifiS_02session.ip_address AS ip_address, '
                              'wp_ifiS_02session.login_attempt AS login_attempt, '
                              'wp_ifiS_02session.attempt_date AS attempt_date, '
                              'wp_ifiS_02session.countrycode AS countrycode, '
                              'wp_ifiS_02session.state AS state, wp_ifiS_02user_recognition.browser AS browser, '
                              'wp_ifiS_02user_recognition.browser_version AS browser_version, '
                              'wp_ifiS_02user_recognition.user_agent AS user_agent, '
                              'wp_ifiS_02user_recognition.platform AS platform, '
                              'wp_ifiS_02user_recognition.login_attempt AS login_attempt, '
                              'wp_ifiS_02user_recognition.login_date AS login_date, '
                              'wp_ifiS_02user_recognition.logout_date AS logout_date, '
                              'wp_ifiS_02user_recognition.duration AS duration, '
                              'wp_ifiS_02user_recognition.loginstatus AS loginstatus, '
                              'wp_ifiS_02user_recognition.subpage AS subpage '
                              'FROM wp_ifiS_02session '
                              'JOIN wp_ifiS_02user_recognition ON '
                              'wp_ifiS_02session.user_id = wp_ifiS_02user_recognition.user_id')'''
            # 167 sec
            '''my_cursor.execute('SELECT wp.session_id, wp.ip_address, wp.login_attempt, wp.countrycode, wp.user_id,'
                              'wp.state, ur.browser, ur.browser_version, ur.user_agent, ur.platform,'
                              'ur.login_attempt, ur.login_date, ur.logout_date, ur.duration, ur.loginstatus,'
                              'ur.subpage FROM wp_ifiS_02session wp '
                              'INNER JOIN wp_ifiS_02user_recognition ur '
                              'ON ur.user_id = wp.user_id '
                              )'''
            # 164 sec
            '''my_cursor.execute('SELECT wp.session_id, wp.ip_address, wp.login_attempt, '
                              'wp.countrycode, wp.user_id, wp.state, reco.*'
                              ' FROM ( SELECT ur.user_id, ur.browser, ur.browser_version, ur.user_agent, ur.platform, '
                              'ur.login_attempt, ur.login_date, ur.logout_date, ur.duration, ur.loginstatus, ur.subpage'
                              ' FROM wp_ifiS_02user_recognition ur'
                              ') reco '
                              'INNER JOIN wp_ifiS_02session wp ON wp.user_id = reco.user_id')'''
            # 165 sec
            ''' my_cursor.execute('WITH sub AS ('
                              'SELECT wp.session_id, wp.ip_address, wp.login_attempt, '
                              'wp.countrycode, wp.user_id, wp.state FROM wp_ifiS_02session wp'
                              ')'
                              'SELECT ur.user_id, ur.browser, ur.browser_version, ur.user_agent, ur.platform, '
                              'ur.login_attempt, ur.login_date, ur.logout_date, ur.duration, '
                              'ur.loginstatus, ur.subpage, sub.*'
                              ' FROM wp_ifiS_02user_recognition ur INNER JOIN sub ON ur.user_id = sub.user_id')'''

            my_cursor.execute('SELECT wp.session_id, wp.ip_address, wp.login_attempt, '
                              'wp.countrycode, wp.user_id, wp.state FROM wp_ifiS_02session wp')

            result_session = my_cursor.fetchall()

            my_cursor.execute('SELECT ur.user_id, ur.browser, ur.browser_version, ur.user_agent, ur.platform, '
                              'ur.login_attempt, ur.login_date, ur.logout_date, ur.duration, ur.loginstatus, ur.subpage'
                              ' FROM wp_ifiS_02user_recognition ur')
            result_ur = my_cursor.fetchall()

            names = ["session_id", "ip_address"]
            # df = DataFrame(result, columns=["session_id", "ip_address"])

            df_session = DataFrame(result_session, columns=["session_id", "ip_address", "login_attempt", "countrycode",
                                                            "user_id", "state"])
            df_ur = DataFrame(result_ur, columns=["user_id", "browser", "browser_version", "user_agent", "platform",
                                                  "login_attempt", "login_date", "logout_date", "duration",
                                                  "loginstatus", "subpage"])

            merged = pd.merge(df_session, df_ur, how='inner', left_on='user_id', right_on='user_id')

            frames = [df_session, df_ur]

            result = pd.concat(frames)

            # pd.set_option("display.max_rows", None)
            # pd.set_option("display.max_columns", None)
            # pd.set_option("display.width", None)

            print(merged)
            # print(result)
            print(len(result_session))
            print(len(result_ur))
            print(time.time() - start_time)

    except Error as e:
        print("Error while connecting to MySQL", e)
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print("MySQL connection is closed")


if __name__ == '__main__':
    import pymysql
    import mysql.connector
    from mysql.connector import Error
    import time
    import pandas as pd
    from pandas import DataFrame
    from sklearn import preprocessing
    from sklearn.model_selection import train_test_split
    import numpy as np
    import mariadb
    from sklearn.neighbors import KNeighborsClassifier
    import random
    from sklearn.ensemble import RandomForestRegressor
    import warnings

    warnings.filterwarnings("ignore", category=FutureWarning)
    from keras.models import Sequential
    from keras.layers import Dense
    from keras.utils import to_categorical
    from sklearn.metrics import mean_squared_error

    # modelBuilding()
    dataImport()
