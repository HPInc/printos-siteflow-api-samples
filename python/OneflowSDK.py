#!/usr/bin/python

__author__ = 'oneflow'

import requests, hmac, hashlib, datetime
from requests.adapters import HTTPAdapter
from requests.packages.urllib3.util.retry import Retry

class OneflowSDK:
    def __init__(self, url, token, secret, options=None):
        self.url = url
        self.token = token
        self.secret = secret
        self.options = options

    def requests_retry_session(self):
        retries = getattr(self.options, 'retries', 3)
        retryFactor = getattr(self.options, 'retryFactor', 0.3 * 60)
        retryStatus = getattr(self.options, 'retryStatus', [429])
        retryMethods = getattr(self.options, 'retryMethods', ['HEAD', 'GET', 'PUT', 'DELETE', 'OPTIONS', 'TRACE', 'POST'])

        session = requests.Session()
        retry = Retry(
            total=retries,
            read=retries,
            connect=retries,
            backoff_factor=retryFactor,
            status_forcelist=retryStatus,
            method_whitelist=retryMethods
        )
        adapter = HTTPAdapter(max_retries=retry)
        session.mount('http://', adapter)
        session.mount('https://', adapter)
        return session

    def create_headers(self, method, path, timestamp):
        string_to_sign = method + ' ' + path + ' ' + timestamp
        signature = hmac.new(bytes(self.secret, 'UTF-8'), string_to_sign.encode(), hashlib.sha256).hexdigest()
        oneflow_auth = self.token + ':' + signature
        return {'content-type': 'application/json',
            'x-oneflow-algorithm': 'SHA256',
            'x-oneflow-authorization': oneflow_auth,
            'x-oneflow-date': timestamp}

    def request(self, method, path, data):
        timestamp = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        url = self.url + path
        headers = self.create_headers(method, path, timestamp)
        session = self.requests_retry_session()
        result = session.post(url, data, headers=headers)
        return result.content