class cnn_config():

    def __init__(self):
        #data hyperparameter
        self.data_window = 60 #dimention of data window
        self.feature_size = 1 #dimention of feature of each data
        self.padding = 1
        self.is_shuffle = True
        self.data_file_path = "A1Benchmark"

        #model hyperparameter
        self.model = 'cnn'
        self.num_classes = 2 #dimention of class
        self.filter_size = 5 #CNN filter window size
        self.num_filters = 64 #CNN filter number
        self.l2_reg_lambda = 0.001 #L2 regularization strength
        self.keep_prob = 0.5 #Dropout keep probability'

        #training hyperparameter
        self.learning_rate = 1e-3 #learning rate
        self.decay_steps = 100000 #Learning rate decay steps
        self.decay_rate = 1 #Learning rate decay rate. Range: (0, 1]
        self.batch_size = 512 #Batch size
        self.num_epochs = 50 #Number of epochs
        self.save_every_steps = 1000 #Save the model after this many steps
        self.num_checkpoint = 10 #Number of models to store'
        self.evaluate_every_steps = 100 #Evaluate the model on validation set after this many steps