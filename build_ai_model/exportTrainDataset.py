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
    """
    This function connect the user to the DB and export the Data for the ML-Algo
    :return: Data
    """
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
            # mycursor.execute('SELECT * FROM `wp_ifiS_02user_recognition` ORDER BY `id` ASC')
            # userRecoTab = mycursor.fetchall()
            # print(userRecoTab)

            sql_query = pd.read_sql_query('SELECT * FROM `wp_ifiS_02user_recognition` ORDER BY `id` ASC', connection)
            print(sql_query)
            print(type(sql_query))



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
    # Some other example server values are
    # server = 'localhost\sqlexpress' # for a named instance
    # server = 'myserver,port' # to specify an alternate port
    # server = '194.94.127.112'
    # database = 'WordPress'
    # username = 'ifis1'
    # password = 'b0QrDr8ShG#e@iMWDwGKlgw3'

    start_time = time.time()

    conn_str = (
      'Driver={MariaDB 10.3 database server};'
      'Server=194.94.127.112;'
      'Database=WordPress;'
      'username=ifis1;'
      'password=b0QrDr8ShG#e@iMWDwGKlgw3;'
      'Trusted_Connection=yes;'
    )
    conn = pyodbc.connect(conn_str)
    # conn = pyodbc.connect('DRIVER={DRIVER={MySQL Server version  5.5.5-10.3.17-MariaDB};SERVER=194.94.127.112;DATABASE=WordPress;UID=ifis1;PWD=b0QrDr8ShG#e@iMWDwGKlgw3')
    cursor = conn.cursor()
    # cursor.execute('SELECT * FROM `wp_ifiS_02session` ORDER BY `id` ASC')
    # for row in cursor.fetchall():
    #    print(row)
    sql_query = pd.read_sql_query('SELECT * FROM `wp_ifiS_02session` ORDER BY `id` ASC', conn)
    # print(sql_query)
    # print(type(sql_query))


def exportDB():
    start_time = time.time()
    # Connect to MariaDB Platform
    try:
        conn = mariadb.connect(
            user="ifis1",
            password="b0QrDr8ShG#e@iMWDwGKlgw3",
            host="194.94.127.112",
            port=3306,
            database="WordPress"

        )
    except mariadb.Error as e:
        print(f"Error connecting to MariaDB Platform: {e}")
        sys.exit(1)

    # Creating a cursor object using the cursor() method
    cursor = conn.cursor()

    # Retrieving many rows

    sql = '''SELECT wp_ifiS_02session.session_id AS session_id, 
              wp_ifiS_02session.ip_address AS ip_address, 
              wp_ifiS_02session.login_attempt AS login_attempt, 
              wp_ifiS_02session.attempt_date AS attempt_date, 
              wp_ifiS_02session.countrycode AS countrycode, 
              wp_ifiS_02session.state AS state, wp_ifiS_02user_recognition.browser AS browser, 
              wp_ifiS_02user_recognition.browser_version AS browser_version, 
              wp_ifiS_02user_recognition.user_agent AS user_agent, 
              wp_ifiS_02user_recognition.platform AS platform, 
              wp_ifiS_02user_recognition.login_attempt AS login_attempt, 
              wp_ifiS_02user_recognition.login_date AS login_date, 
              wp_ifiS_02user_recognition.logout_date AS logout_date, 
              wp_ifiS_02user_recognition.duration AS duration, 
              wp_ifiS_02user_recognition.loginstatus AS loginstatus, 
              wp_ifiS_02user_recognition.subpage AS subpage
              FROM wp_ifiS_02session JOIN wp_ifiS_02user_recognition 
              ON wp_ifiS_02session.user_id = wp_ifiS_02user_recognition.user_id'''

    sql1 = ''' SELECT wp.session_id, wp.ip_address, wp.login_attempt, 
              wp.countrycode, wp.user_id, wp.state, reco.*
              FROM ( SELECT ur.user_id, ur.browser, ur.browser_version, ur.user_agent, ur.platform, 
              ur.login_attempt, ur.login_date, ur.logout_date, ur.duration, ur.loginstatus, ur.subpage
              FROM wp_ifiS_02user_recognition ur
              ) reco 
              INNER JOIN wp_ifiS_02session wp ON wp.user_id = reco.user_id'''

    # Executing the query
    cursor.execute(sql)

    # Fetching 1st row from the table
    result = cursor.fetchall();
    print(type(result))
    print(result)

    try:
        cursor.execute("some MariaDB query")
    except mariadb.Error as e:
        print(f"Error: {e}")

    print("--- %s seconds ---" % (time.time() - start_time))
    # Close Connection
    conn.close()


if __name__ == '__main__':
    import time
    import pymysql
    import mysql.connector
    from mysql.connector import Error, cursor
    import pyodbc
    import pandas as pd
    import mariadb
    import sys
    import sqlparse

    exportDB()
    # exportedDB()
    # exportDBUBAIFIS()
