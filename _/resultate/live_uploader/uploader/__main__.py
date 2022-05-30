import os
import sys

from . import webui, updater


def ask_string(question):
    print(question)
    return input()


def ask_path(question):
    while True:
        print(question)
        res = input()
        if os.path.realpath(res):
            return os.path.realpath(res)
        else:
            print(f'No such file: {res!r}')


def ask_options(question, options):
    res = None
    if isinstance(options, dict):
        while True:
            print(question)
            for k in options:
                print(f'{k:6} {options[k]}')
            res = input()
            if res in options:
                return res
            else:
                print(f'No such option: {res!r}')
    else:
        while True:
            print(question)
            for k in range(len(options)):
                print(f'{k:6} {options[k]}')
            res = input()
            try:
                res_ind = int(res)
                if 0 <= res_ind < len(options):
                    return res_ind
                else:
                    print(f'No such option: {res_ind!r}')
            except ValueError:
                print(f'Invalid value: {res!r}')


res = ask_options('What setup should be carried out?', [
    'Manual Setup',
    'OLZ Setup',
])

if res == 0:
    gui_url = ask_string('What URL should be opened on this computer?')
    server_url = updater.ask_server_url('Where should results be uploaded?')
    upload_file = ask_path('Which file should be uploaded?')
elif res == 1:
    gui_url = 'index.html?file=data/export.xml'
    server_url = (
        'https://olzimmerberg.ch/resultate/update.php?file=' +
        ask_string('How should the online file be called?') + '.xml'
    )
    upload_file = os.path.realpath(os.path.join(
        os.path.dirname(__file__),
        '..',
        'public_html',
        'data',
        'export.xml',
    ))
else:
    print('Unknown option selected')
    sys.exit()

httpd = webui.start_server()
webui.start_webbrowser(
    httpd.server_port,
    httpd.instance_hash,
    gui_url,
)
updater.start_updater(
    server_url,
    upload_file,
)
