import pandas as pd
import numpy as np
import os
from sklearn import preprocessing
from sklearn.model_selection import train_test_split
from pathlib import Path


class DataLoader:
    def __init__(self):
        self.data_folder = Path("C:/Users/Eduard/Desktop/ydata-labeled-time-series-anomalies-v1_0/A1Benchmark")
        self.dataFrame = pd.DataFrame()
        self.dataFrame_stream = []
        self.labels = []
        self.x_data = []
        self.y_data = []
        self.dataFrame_window = 60
        self.num_batches = 0
        self.sequences = []

    def load_data(self):
        for i in range(1, 68):
            pathname = os.path.join(self.data_folder, "real_" + str(i) + ".csv")
            tmp = pd.read_csv(pathname)
            self.dataFrame = pd.concat([self.dataFrame, tmp])
        self.dataFrame = self.dataFrame.reset_index()
        self.dataFrame = self.dataFrame.drop("timestamp", axis=1)

        number = preprocessing.LabelEncoder()
        """for i in self.dataFrame.keys():
            self.dataFrame[i] = number.fit_transform(self.dataFrame[i].astype('str'))"""

        min_max_scaler = preprocessing.MinMaxScaler()
        #self.dataFrame = min_max_scaler.fit_transform(self.dataFrame["value"])

        #MinMaxScale and Normalize only the value
        minX = self.dataFrame["value"].min()
        maxX = self.dataFrame["value"].max()
        norm = self.dataFrame["value"].apply(lambda x: float(x - minX) / (maxX - minX))
        self.dataFrame = pd.DataFrame(self.dataFrame, columns=["index", "is_anomaly", "value"])
        self.dataFrame = self.dataFrame.drop("value", axis=1)
        self.dataFrame["value"] = norm

        print(self.dataFrame)

        for i in range(self.dataFrame.shape[0]):
            features = []
            features.append(self.dataFrame["value"][i])
            self.dataFrame_stream.append(features)
            self.labels.append(self.dataFrame["is_anomaly"][i])

        for i in range(0, self.dataFrame.shape[0] - self.dataFrame_window + 1, 1):
            sequence = []
            anom = 0
            for j in range(60):
                sequence.append(self.dataFrame_stream[j])
                if self.labels[i * 1 + j] == 1:
                    anom = 1
            self.x_data.append(sequence)
            self.y_data.append([anom])
        self.x_data = np.array(self.x_data)
        self.y_data = np.array(self.y_data)

        # split dataset into training and testing
        x_train, x_test, y_train, y_test = train_test_split(self.x_data, self.y_data, test_size=0.3, random_state=42)

        num_batches_train = int(x_train.shape[0] / 512)
        num_batches_test = int(x_test.shape[0] / 512)
        print(int(num_batches_train))
        x_train = x_train[:num_batches_train * 512]
        #y_train = y_train[:num_batches_train * 512]
        #x_test = x_test[:num_batches_test * 512]
        #y_test = y_test[:num_batches_test * 512]

        sequence_batch_x_train = np.split(x_train, num_batches_train, 0)
        sequence_batch_y_train = np.split(y_train, num_batches_train, 0)
        pointer = 0
        self.num_batches = num_batches_train

        return x_train, x_test, y_train, y_test


if __name__ == '__main__':
    dataloader = DataLoader()
    print(dataloader.load_data())

    print("hello")
