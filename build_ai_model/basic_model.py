import sys
import time
from mysql.connector import Error
from mysql.connector import pooling
from pathlib import Path
import numpy as np
import json

   
def get_dataset():
    sys.stdout.write('Loading Dataset\n')
    sys.stdout.flush()
    
    try:
        connection_pool = pooling.MySQLConnectionPool(pool_name="sessions_logs_pool",
                                                    pool_size=3,
                                                    pool_reset_session=True,
                                                    host='194.94.127.112',
                                                    database='WordPress',
                                                    user='ifis1',
                                                    password='b0QrDr8ShG#e@iMWDwGKlgw3')

        print("Printing connection pool properties ")
        print("Connection Pool Name - ", connection_pool.pool_name)
        print("Connection Pool Size - ", connection_pool.pool_size)

        # Get connection object from a pool
        connection_object = connection_pool.get_connection()

        if connection_object.is_connected():
            db_Info = connection_object.get_server_info()
            print("Connected to MySQL database using connection pool ... MySQL Server version on ", db_Info)

            cursor = connection_object.cursor()
            cursor.execute('SELECT session_id, ip_address, session_date, countrycode, state, user_agent, '
                       'platform, browser, subpage, ip_blacklisten, download_link, time_spent '
                       'FROM wp_ifiS_02session_logs')
            record = cursor.fetchone()
            print("Your connected to - ", record)

    except Error as e:
        print("Error while connecting to MySQL using Connection pool ", e)
    finally:
        # closing database connection.
        if connection_object.is_connected():
            cursor.close()
            connection_object.close()
            print("MySQL connection is closed")

def main():
    get_dataset()

    
if __name__ == '__main__':
    # execute only if run as a script
    start = time.time()
    main()
    end = time.time()
    hours, rem = divmod(end-start, 3600)
    minutes, seconds = divmod(rem, 60)
    print("{:0>2}:{:0>2}:{:05.2f}".format(int(hours),int(minutes),seconds))
