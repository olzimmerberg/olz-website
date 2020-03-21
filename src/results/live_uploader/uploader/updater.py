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
            with urlopen(url) as fh:
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

        if new_file:
            print("Patch: All new...")
            with open(file_path, 'rb') as fpr:
                with open(file_path + '.patch', 'wb+') as fpw:
                    fpw.write(fpr.read())
                    print("Patch: All new")
        else:
            print("Diff -u...")
            with subprocess.Popen(
                'diff' + ' ' +
                '-u' + ' ' +
                previous_file_path + ' ' +
                file_path,
                shell=True,
                stdout=subprocess.PIPE,
            ) as proc:
                with open(file_path + '.patch', 'wb+') as fpw:
                    fpw.write(proc.stdout.read())

        if new_file:
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
                        )) as fh:
                            result = json.loads(fh.read().decode())
                            if result[0]:
                                print("Successful raw-update.")
                                time.sleep(10)
                            else:
                                raise Exception("Server error")
                    except Exception as exc:
                        print("Failed raw-update. No idea what to do...")
                        print(exc)
                        os.unlink(previous_file_path)
        else:
            with open(file_path + '.patch', 'rb') as fpr:
                diff_content = fpr.read()
                if diff_content:
                    print("Patch -u...")
                    with subprocess.Popen(
                        'patch' + ' ' +
                        '-u' + ' ' +
                        previous_file_path + ' ' +
                        '-i' + ' ' +
                        file_path + '.patch' + ' ' +
                        '-o' + ' ' +
                        previous_file_path + '.patched',
                        shell=True,
                        stdout=subprocess.PIPE,
                    ) as proc:
                        proc.stdout.read()
                        os.rename(
                            previous_file_path,
                            backup_file_path,
                        )
                        os.rename(
                            previous_file_path + '.patched',
                            previous_file_path,
                        )

                        try:
                            with urlopen(Request(
                                url=server_url,
                                method='POST',
                                data=b'diff=' + urlencode(
                                    base64.b64encode(diff_content),
                                ).encode(),
                            )) as fh:
                                result = json.loads(fh.read().decode())
                                if result[0]:
                                    print("Successful diff-update.")
                                    time.sleep(10)
                                else:
                                    raise Exception("Server error")
                        except Exception as exc:
                            print("Failed diff-update!")
                            print(exc)
                            os.unlink(previous_file_path)
                else:
                    print("No difference")
        time.sleep(10)
