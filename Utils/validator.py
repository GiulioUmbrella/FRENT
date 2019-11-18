#!/usr/bin/env python3

import requests
from bs4 import BeautifulSoup
import os
from sys import argv, exit

HEADER = '\033[95m'
OKBLUE = '\033[94m'
OKGREEN = '\033[92m'
WARNING = '\033[93m'
FAIL = '\033[91m'
ENDC = '\033[0m'
BOLD = '\033[1m'
UNDERLINE = '\033[4m'
ITALIC = '\033[3m'


url = "https://validator.w3.org/check"
_, columns = os.popen('stty size', 'r').read().split()
divisore = "#"*int(columns)

def download_w3c_response(file_to_validate):
    with open(file_to_validate, 'rb') as file:
        header = {'content-type': 'text/html; charset=UTF-8'}
        payload = {'fragment': file.read()}
        response = requests.post(url=url, data=payload)
        return response.text

def validate_file(file_to_validate):
    soup = BeautifulSoup(download_w3c_response(file_to_validate), 'html.parser')
    warns = soup.find(id="warnings")
    w = "0"
    return_warns = []
    return_errors = []
    if warns:
        warns_list = warns.find_all('li')
        for index, warning in enumerate(warns_list):
            return_warns.append(warning.find(class_='msg').get_text())
        w = "-1"

    errors = soup.find(id="error_loop")
    if errors:
        errors_list = errors.find_all('li')
        for index, error in enumerate(errors_list):
            return_errors.append([
            ' '.join(error.find('em').get_text().split()),
            ' '.join(error.find(class_='msg').get_text().split()),
            ' '.join(error.find(class_='input').get_text().split())
            ])
        w = "-2"
    return w, return_warns, return_errors

def result_for(file):
    value_to_return = 0
    validation, warns, errors = validate_file(file)

    print(file + "...", end='')
    if validation == "0":
        print(OKGREEN + "OK" + ENDC)
    elif validation == "-1":
        print(WARNING + "WARNING" + "(" + str(len(warns)) + ")" + ENDC + " (il numero si riferisce agli errori mostrati da w3c validator)")
    elif validation == "-2":
        value_to_return = -1
        print(FAIL + "FAIL" + "(" + str(len(errors)) + ")" + ENDC + " (il numero si riferisce agli errori mostrati da w3c validator)")
        if len(argv)>1 and "-v" in argv:
            for error in errors:
                print(OKBLUE + "At: " + ENDC, error[0])
                print(OKBLUE + "Codice errato (o vicino a): " + ENDC, error[2])
                print(OKBLUE + "Messaggio: " + ENDC, error[1])
                print(divisore)
    return value_to_return

def main():
    value_to_return = 0
    os.chdir("../html")

    if len(argv)>1 and argv[1] == "-f":
        for file in argv[2:]:
            if os.path.exists(file) and result_for(file) != 0:
                value_to_return = -1
    else:
        for file in os.listdir("./"):
            if result_for(file) != 0:
                value_to_return = -1
    return value_to_return

if __name__ == '__main__':
    if main() != 0:
        exit(1) #c'è stato almemeno un errore
    else:
        exit(0)
