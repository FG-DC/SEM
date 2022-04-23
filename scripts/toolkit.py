import pwinput
import requests
import time

URL = 'http://backend.test/api'

def auth():
    while (True):
        username = input('Username: ')
        password = pwinput.pwinput()

        DATA = {'username': username, 'password': password}
        response = requests.post(URL + '/login', data=DATA)
        if (response.status_code == 200):
            responseJSON = response.json()
            return {responseJSON['access_token'], responseJSON['refresh_token']}
        
        print('Invalid credentials. Try again.\n')

def get_equipments():
    response = requests.get(URL + '/users/1/equipments', headers=HEADERS)

def print_menu():
    menu_options = {
        1: 'One device reads',
        2: 'Exit'
    }

    print('-------- MENU ---------')
    for key in menu_options.keys():
        print(key, '-', menu_options[key])

def one_device_reads():
    print('Turn ON the device. Press Ctrl + C to stop.')
    count = 1
    try:
        while (True):
            print(f'[{count}] - 0 W')
            time.sleep(2)
            count += 1
    except KeyboardInterrupt:
        print('Reads completed. Posting to API... ', end='')
        print('Done')

# START
print('---- S.E.M Toolkit ----')

# AUTHENTICATION
accessToken, refreshToken = auth()
HEADERS = {'Authorization': 'Bearer ' + accessToken}
print('-----------------------')

# MENU
while (True):
    print()
    print_menu()
    option = int(input('Option: '))
    print()
    if option == 1:
        one_device_reads()
    elif option == 2:
        exit()