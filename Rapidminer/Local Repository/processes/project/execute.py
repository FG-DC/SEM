import rapidminer
connector = rapidminer.Studio("C:\Program Files\RapidMiner\RapidMiner Studio")
#project = rapidminer.Project("D:\GitRepository\SEM\Rapidminer\Local Repository\processes\project\smart_monitoring.rmp")
df = connector.run_process("D:\GitRepository\SEM\Rapidminer\Local Repository\processes\project\smart_monitoring.rmp")
