import fhmm_model as fhmm
import pandas as pd
import matplotlib.pyplot as plt



def rm_main(dataset,testDataset):    
    list_of_appliance = ['appliance1','appliance2','appliance3','appliance4','appliance5']
    #list_of_appliance = ['app1','app2','app3','app4']
    fhmms = fhmm.FHMM()
    fhmms.train(dataset, list_of_appliance)
    fhmms.save("fhmm_trained_model")
    prediction = fhmms.disaggregate(testDataset)
    #print(prediction)
    prediction.plot()
    plt.show()
    return prediction