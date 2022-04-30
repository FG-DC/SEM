import fhmm_model as fhmm
import pandas as pd
import matplotlib.pyplot as plt



def rm_main(dataset,testDataset):
    return dataset
    list_of_appliance = []
    #list_of_appliance = ['app1','app2','app3','app4']
    fhmms = fhmm.FHMM()
    fhmms.train(dataset, dataset.keys()[2:])
    fhmms.save("fhmm_trained_model")
    prediction = fhmms.disaggregate(testDataset)
    #print(prediction)
    prediction.plot()
    plt.show()
    return prediction