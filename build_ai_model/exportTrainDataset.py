"""
 This is the Tool to analyse a Use Behavior on a Wordpress-Website
 The User can be blocker, if the Machine Learning Algo evaluate the use ask risk
"""


def export():
    connection = pymysql.connect(host='194.94.127.112',
                                 user='root',
                                 password='AYw0D#r/wkf&cDttoObZXn.I1InZR~HX',
                                 db='WordPress',
                                 charset='utf8mb4',
                                 port=3306)
    # cursorclass=pymysql.cursors.DictCursor)

    # cursor = connection.cursor()

    # Selecting.
    # query = "SELECT `id` FROM `table` ORDER BY RAND() LIMIT 1"
    """query = "SELECT `wp_ifiS_02session.session_id` AS session_id, wp_ifiS_02session.ip_address AS ip_address,
    wp_ifiS_02session.login_attempt AS login_attempt, wp_ifiS_02session.attempt_date AS attempt_date,
    wp_ifiS_02session.countrycode AS countrycode, wp_ifiS_02session.state AS state,
    wp_ifiS_02user_recognition.browser AS browser, wp_ifiS_02user_recognition.browser_version AS browser_version,
    wp_ifiS_02user_recognition.IP AS IP, wp_ifiS_02user_recognition.user_agent AS user_agent,
    wp_ifiS_02user_recognition.platform AS platform, wp_ifiS_02user_recognition.login_attempt AS login_attempt,
    wp_ifiS_02user_recognition.login_date AS login_date, wp_ifiS_02user_recognition.logout_date AS logout_date,
    wp_ifiS_02user_recognition.duration AS duration, wp_ifiS_02user_recognition.loginstatus AS loginstatus,
    wp_ifiS_02user_recognition.subpage AS subpage FROM wp_ifiS_02session, wp_ifiS_02user_recognition
    WHERE
    wp_ifiS_02session.user_id = wp_ifiS_02user_recognition.user_id
    AND
    wp_ifiS_02session.login_attempt = wp_ifiS_02user_recognition.login_attempt"""

    # cursor.execute(query)

    # if cursor.rowcount == 0:
    #    print('No results matched your query.')
    # else:
    #    print(cursor.fetchone()['id'])

    # Inserting
    # query = "INSERT INTO `table` (id) VALUES (1)"
    # cursor.execute(query)

    connection.commit()  # You need this if you want your changes 'commited' to the database.


def exportDBUBAIFIS():
    start_time = time.time()

    try:
        connection = mysql.connector.connect(host='194.94.127.112',
                                             database='WordPress',
                                             user='ifis1',
                                             password='b0QrDr8ShG#e@iMWDwGKlgw3')
        if connection.is_connected():
            db_Info = connection.get_server_info()
            print("Connected to MySQL Server version ", db_Info)
            cursor = connection.cursor()
            cursor.execute("select database();")
            record = cursor.fetchone()
            print("You're connected to database: ", record)

            mycursor = connection.cursor()

            '''mycursor.execute('SELECT wp_ifiS_02session.session_id AS session_id, wp_ifiS_02session.ip_address AS ip_address, '
                             'wp_ifiS_02session.login_attempt AS login_attempt, wp_ifiS_02session.attempt_date AS attempt_date, ' 
                             'wp_ifiS_02session.countrycode AS countrycode, wp_ifiS_02session.state AS state,' 
                            'wp_ifiS_02user_recognition.browser AS browser, wp_ifiS_02user_recognition.browser_version AS browser_version,' 
                            'wp_ifiS_02user_recognition.IP AS IP, wp_ifiS_02user_recognition.user_agent AS user_agent,' 
                            'wp_ifiS_02user_recognition.platform AS platform, wp_ifiS_02user_recognition.login_attempt AS login_attempt,' 
                            'wp_ifiS_02user_recognition.login_date AS login_date, wp_ifiS_02user_recognition.logout_date AS logout_date,' 
                            'wp_ifiS_02user_recognition.duration AS duration, wp_ifiS_02user_recognition.loginstatus AS loginstatus,' 
                            'wp_ifiS_02user_recognition.subpage AS subpage FROM wp_ifiS_02session JOIN wp_ifiS_02user_recognition,'
                            'ON wp_ifiS_02session.user_id = wp_ifiS_02user_recognition.user_id,'
                            'ON wp_ifiS_02session.login_attempt = wp_ifiS_02user_recognition.login_attempt') '''
            mycursor.execute('SELECT * FROM `wp_ifiS_02user_recognition` ORDER BY `id` ASC')
            userRecoTab = mycursor.fetchall()
            print(userRecoTab)

        def fetchUserRecoTab():
            mycursor.execute('SELECT * FROM `wp_ifiS_02user_recognition` ORDER BY `id` ASC')
            userRecoTab = mycursor.fetchall()
            print(userRecoTab)

        def fetchUserSessionTab():
            mycursor.execute('SELECT * FROM `wp_ifiS_02session` ORDER BY `id` ASC')
            userSessionTab = mycursor.fetchall()
            print(userSessionTab)

        # userReco = fetchUserRecoTab()
        # userSession = fetchUserSessionTab()
        # print(userReco)
        # print(userSession)
        # merged = []
        # for user in userReco[1]:
        #    uid = user
        #    uid += (['', ''])  # ip and user_id
        #    for session in userSession[1]:
        #        if uid[0] == session[1]:
        #            uid[3] = session[2]
        #            break
        #    merged.append(uid)
        # return merged

    except Error as e:
        print("Error while connecting to MySQL", e)
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print("MySQL connection is closed")
    print("--- %s seconds ---" % (time.time() - start_time))


def exportedDB():
    """
    This function connect the user to the DB and export the Data for the ML-Algo
    :return: Data
    """
    mydbconnect = mysql.connector.connect(host="localhost", user="root", passwd="", database="tmudb")

    mycursor = mydbconnect.cursor()

    mycursor.execute("select * from wp_users")

    result = mycursor.fetchall()

    for i in result:
        print(i)


def exportDB():
    mydbconnect = mysql.connector.connect(host="194.94.127.112", user="ifis1",
                                          passwd="b0QrDr8ShG#e@iMWDwGKlgw3", database="WordPress")

    mycursor = mydbconnect.cursor()

    mycursor.execute("SELECT * FROM `wp_ifiS_02session` ORDER BY `id` ASC")

    result = mycursor.fetchall()

    for i in result:
        print(i)


def something(duration=0.000001):
    """
    Function that needs some serious benchmarking.
    """
    time.sleep(duration)
    # You may return anything you want, like the result of a computation
    return 123


def test_my_stuff(benchmark):
    # benchmark something
    result = benchmark(something)

    # Extra code, to verify that the run completed correctly.
    # Sometimes you may want to check the result, fast functions
    # are no good if they return incorrect results :-)
    assert result == 123


if __name__ == '__main__':
    import time
    import pymysql
    import mysql.connector
    from mysql.connector import Error
    import sqlparse

    # exportdb()
    # exportedDB()
    exportDBUBAIFIS()
