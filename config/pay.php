<?php

return [
    'alipay' => [
        'app_id' => '2016101300677014',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiK5alE1F0PBKgmBLTWlesmPzr5+KzWx1t8owbVF10SGEG+Xzp7OYrBKx/hdbGka+nwJOLqCQmKRDTbWV6O5UAGDn6jC85URixohc5bPHpdOAFyffpNh6TXwn/wfBjURUGa59mKubm7CCn4spUqMfWndOLZV/3MUyVhaRQePPw4bQNXp4WnAjITIWLlvzaKqFtRySadaYV0akuRWPpjr/oo5sCLZxpPSfv9BqwFImfHidVx2UzRZhsnkHFqyjgvLTI3DNolzT81dR427wjt1Tuw85qbHt+Y+GCZPD1UJ743naamVr0hoWVgmG7b0T+IAcCEUuaT10Jh/6Id3qoCgzjwIDAQAB',
        'private_key' => 'MIIEogIBAAKCAQEAym0eb6MGeqFXLQ79WFKTQ4uycbWUkLQU3qSkn3DbRTIkzEUYiMh1wv71eBJwhFAReyKYeeQvvWDyliL6x2/0zXVZccojc8AXCZN3QM/RFTSfk4nYT45h9cEy7kjzSrJ00iyKl0/r+fwVNT0Uz1giV0T1SMHP6iKj9QO82pq+xMQuvNpZizDEgYHPk9FasER2kPiMevZ1rEA6g4IFPj2LCpI27Hk4/JANNmyiyhKCUqPWaIE+cA7B++o415WhvtNMs6nbf0T5bjOHFFc8tnHGmzF2BwwrO77nEgFqARtiwX7iBDhOn17FrQcJpIH2ddUfi8nhPAMmy3ma3pJDxlCNFQIDAQABAoIBABhrZXqOLh/pCr0yy7k17PH/Dj3Na+iw8LuTHRDm6mzEic/inf0SQbKOU0py3wP3LMSv/bjmoCO2aE1YVuxsQxnuCCZD/lbeW2kaUw6Mg2qNeJqxzFh8krC059H7Fu5x7MnV+bobOBpmIKDVh4cj2LCybuyiBNT37NOH6eZtOIo/8Xl4jhaubi/HKi7vUPG8oN3PbOWI7Y86st0tPL5BlaI1Sj48jTlFcaJSv2xzCriNsZyZH4Qzzkc5723mTsdJgOxwz7myMgZR7nqee7L6qAr1fcTEgldWMTlYLbmnpNmQMUMikxQKzsF5f/5hXma1Cf6rRo2V/5hNJ0eoLwCIwsECgYEA6hiqhYADFq+GHHPN/IqG6HxMwLp/creaoPZV7oyPIGyL4m6/qU+J1Eyvdi3P1pVAJAce9KM/C/rFZT+Leqfl2m0+FxeLZzYuCkdcL+pvZ3a9PPXqrAum1xo2j5DZ03rIb4Ia384XY4R+1Wal94yYTy1td2P4hLejVdOApNgFwokCgYEA3V3bAXc/ZTdrenS8loUlq8q0Yt9tIegCl++8LzCquon70vwMNrM4QNJIe7Whs7fvo8y72VUGjEPXOMF+w+OQHnnqHETzaWcao3H129rkjGw5ZXwKy2qe4GI2xm7Jef7b6sqF4qekKPccdYZIYVhirSXpvzxWn80YA/88Jb1nwy0CgYArUTskjuaDaSFY3HjuVTfXh0CwnRH+SUk8pbiK9sl5R3yu/q1KiCRJ+5KwPJPr8hw3TnYE8Lw23EVwkcyeerkGrRZDMaEjDRyB3GiLmUbaD1e/EwZEpbdOsFQORtB32I2ETL/qT/l1+ct6c48cepCofhB2ejI/ZLe9pvB0EGEoeQKBgCciao9Nx5VbQNL7REPP8iL2yQEZwq9V1u+JYFlvXx9vOWfJS1d4Q5+iDVJhf97Hy8PHdAYt8+RkMe5EtuZmmpnor6yju+yMX7c5dF+TyQfSMitwzG+9v6yncEuQVmoM1lAh0Z2KSYk8dnmIbc9X0soNg89dGWtS1MYQH1AEtg1ZAoGARoVhNagSMVkm5ufNX1TdJGsGGYYUL3HF94mYHx0L0R5npJUc8ojL1OCd1x9PKRph3pcK2lpVBfwKlMKOJ6nVDySCcmDYoezm7kX6k2EW4hr9yRy9g4/5dqxNQBg8CJoVYKbptbtPrzfTylsvo/9+m62JmpCJ7h9fQv4yE3pJzls=',
        'log' => [
            'file' => storage_path('log/ali_pay.log'),
        ],
    ],

    'wechat' => [
        'app_id' => '',
        'mch_id' => '',
        'key' => '',
        'cert_client' => '',
        'cert_key' => '',
        'log' => [
            'file' => storage_path('log/wechat_pay.log')
        ],
    ]
];
