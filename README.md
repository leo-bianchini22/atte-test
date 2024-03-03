# 勤怠管理システム　Atte

## 作成した目的
アプリを利用し、勤怠管理を柔軟に行うため。

## 環境構築
#### i.Dockerビルド

1. git clone git@github.com:leo-bianchini22/atte-test.git
2. mv atte-test "任意のディレクトリ名"
3. docker-compose up -d --build

#### Lalavel環境構築
1. docker-compose exec php bash
2. composer install
3. cp .env.example .env
  ( .env.exampleファイルから.env作成、環境変数の変更を行う )
5. php artisan key:generate
6. php artisan migrate
