# Energy Monitoring

Energy Monitoring is a project which main purpose is to determine the various equipment consumptions from the aggregate consumption.

## Installation

Use the package manager [pip](https://pip.pypa.io/en/stable/).

```bash
pip install pwinput
pip install requests
pip install paho-mqtt
pip install numpy
pip install hmmlearn
pip install matplotlib
```

## Usage

### dataset.py

Used to get all training examples and create an updated copy locally on /train directory.

### predictions.py

Used to get predictions from the training dataset + testing data.

```bash
py toolkit.py
```

## License
[MIT](https://choosealicense.com/licenses/mit/)