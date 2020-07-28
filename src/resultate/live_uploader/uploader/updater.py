import base64
from datetime import datetime
import json
import os
import subprocess
import threading
import time
from urllib.parse import quote as urlencode
from urllib.request import Request, urlopen


def ask_server_url(question):
    while True:
        print(question)
        url = input()
        try:
            with urlopen(Request(
                url=url,
                headers={'User-Agent': 'Mozilla'},
            )) as fh:
                res = fh.read()
                if res != b'OK':
                    print(res)
                    raise Exception()
            return url
        except:
            print(f'Not a valid results destination: {url!r}')


def start_updater(
    server_url,
    file_path,
):
    t = threading.Thread(
        target=_run_updater,
        args=(server_url, file_path),
    )
    t.start()
    return t


def _run_updater(server_url, file_path):
    """update information on website"""

    previous_file_path = "_".join(os.path.splitext(file_path))

    while True:
        if not os.path.isfile(file_path):
            time.sleep(10)
            continue
        new_file = not os.path.isfile(previous_file_path)
        backup_file_path = ("_" + str(datetime.now())).join(
            os.path.splitext(file_path),
        )

        should_upload = True
        if new_file:
            should_upload = True
        else:
            with open(file_path, 'rb') as fpr:
                with open(previous_file_path, 'rb') as fprp:
                    should_upload = fpr.read() != fprp.read()

        if should_upload:
            print('Upload...')
            with open(file_path, 'rb') as fpr:
                new_content = fpr.read()
                with open(previous_file_path, 'wb+') as fpw:
                    fpw.write(new_content)

                    try:
                        with urlopen(Request(
                            url=server_url,
                            method='POST',
                            data=b'new=' + urlencode(
                                base64.b64encode(new_content),
                            ).encode(),
                            headers={'User-Agent': 'Mozilla'},
                        )) as fh:
                            content = fh.read().decode()
                            result = json.loads(content)
                            if result[0]:
                                print("Successful raw-update.")
                                time.sleep(10)
                            else:
                                raise Exception(f"Server error: {content}")
                    except Exception as exc:
                        print("Failed raw-update. No idea what to do...", server_url)
                        print(exc)
                        os.unlink(previous_file_path)
        else:
            print('No difference')
        time.sleep(10)
