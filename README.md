testfire2.stat.ink
==================

The stat.ink application for [Global Testfire](https://twitter.com/SplatoonJP/status/830695378081046528) of [Splatoon 2](https://www.nintendo.co.jp/software/switch/splatoon2/).


動作要件
--------

- Linux サーバ/デスクトップ
  - Docker for Mac/Windows での動作は未確認
  - Linux Kernel 4.4 以上を推奨
    - 一部で利用しているイメージが Kernel 4.4 で構築されているため。（システムコールの呼び出し周りではまるかも）

- Docker
  - Docker Engine
  - [Docker Compose](https://docs.docker.com/compose/)

- Make など一部の基本的な Unix ツール


環境構築
--------

1. Docker の環境を構築します。
  - [Get Docker](https://www.docker.com/get-docker) あたりから適当に情報を探してください。

2. Docker Compose の環境を構築します。（[説明をよむ](https://docs.docker.com/compose/install/)か、次のように pip でインストール）
  1. `yum install python34-pip` or `apt-get install python3-pip`
  2. `pip3 install docker-compose`

3. [GitHub](https://github.com/) のアカウントを持っていなければ取得します。

4. GitHub の API アクセス用トークンを取得し、保存します。このトークンは webapp 用コンテナの構築時に [composer](https://getcomposer.org/) が大量に API アクセスするため必要になります。
  1. [Settings - Personal access tokens](https://github.com/settings/tokens) にアクセスします。
  2. Generate new token ボタンを押し、新しいトークンを発行します。権限は最小の何もチェックしない状態で大丈夫です。
  3. 発行したトークンを secrets/github-token.txt に保存します。例えば `echo "0123456789abcdef0123456789abcdef01234567" > secrets/github-token.txt`

5. Docker ネットワークを構成します。
  1. `docker network create expose`

6. コンテナをビルドします。
  1. `make`
    - 2回目以降は `docker-compose build` でも大丈夫ですが、初回は `make` で準備をする必要があります。
    - この make で秘密の情報を含む次のファイルが生成されます。これらのファイルの内容が流出しないように注意してください:
      - `docker-compose.yml` : 前のステップで発行した GitHub のトークンを含みます。
      - `webapp/config/cookie-secret.php` : Cookie の改ざん検査のための秘密のキーが生成されます。

7. コンテナ群を立ち上げます。
  1. `docker-compose up -d`


ライセンス
----------

Copyright (C) 2015-2017 AIZAWA Hina. All rights reserved.

Mainly, licensed under the MIT License.

Some documents are licensed under the CC-BY 4.0 License.
