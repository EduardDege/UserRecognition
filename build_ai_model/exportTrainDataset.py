"""
 This is the Tool to analyse a Use Behavior on a Wordpress-Website
 The User can be blocker, if the Machine Learning Algo evaluate the use ask risk
"""

def mariadbtest():
    start_time = time.time()
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
        cursor.execute('SELECT wp.session_id, wp.ip_address, wp.login_attempt, '
                   'wp.countrycode, wp.user_id, wp.state FROM wp_ifiS_02session wp')

        result_session = cursor

        cursor = conn2.cursor()
        cursor.execute('SELECT ur.user_id, ur.browser, ur.browser_version, ur.user_agent, ur.platform, '
                   'ur.login_attempt, ur.login_date, ur.logout_date, ur.duration, ur.loginstatus, ur.subpage'
                   ' FROM wp_ifiS_02user_recognition ur')

        result_ur = cursor

        df_session = DataFrame(result_session, columns=["session_id", "ip_address", "login_attempt", "countrycode",
                                                    "user_id", "state"])
        df_ur = DataFrame(result_ur, columns=["user_id", "browser", "browser_version", "user_agent", "platform",
                                          "login_attempt", "login_date", "logout_date", "duration",
                                          "loginstatus", "subpage"])

        merged = pd.merge(df_session, df_ur, how='inner', left_on='user_id', right_on='user_id')
        frames = [df_session, df_ur]

        concat = pd.concat(frames)

        merged = merged.dropna()

        print(len(merged))

        train, test = train_test_split(merged, test_size=0.2)

        print(len(train))
        print(len(test))
        conn1.close()
        conn2.close()

        x_train = train.values
        x_test = test.values

        # encode strings to floats
        ohe = preprocessing.OneHotEncoder()

        x_train_encoded = ohe.fit_transform(x_train).toarray()

        #print(ohe.categories_)

        x_test_encoded = ohe.fit_transform(x_test).toarray()

        print(len(x_train_encoded))
        '''np.savetxt("rescaledX_train.csv", x_test_encoded[0:1000, 0:16], delimiter=",")'''

        # inverse = ohe.inverse_transform(x_test_encoded)

        # print(inverse)

        print(time.time() - start_time)

    except mariadb.Error as e:
        print(f"Error:{e}")


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


def build_model(optimizer):
    model = Sequential()
    model.add(Dense(output_dim=6, init='uniform', activation='relu', input_dim=11)) #input_dim = number of input variables
    model.add(Dense(output_dim=6, init='uniform', activation='relu'))
    model.add(Dense(output_dim=1, init='uniform', activation='sigmoid'))
    model.compile(optimizer=optimizer, loss='binary_crossentropy', metrics=['accuracy'])
    return model


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
    import numpy as np
    import matplotlib.pyplot as plt
    import pandas as pd
    from sklearn.preprocessing import StandardScaler, LabelEncoder, OneHotEncoder
    from sklearn.model_selection import train_test_split, GridSearchCV, cross_val_score
    from sklearn.metrics import confusion_matrix
    from keras.models import Sequential
    from keras.layers import Dense
    from keras.wrappers.scikit_learn import KerasClassifier

    # exportDBUBAIFIS()
    mariadbtest()
