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
4. php artisan key:generate
5. php artisan migrate

## 機能一覧
* 会員登録機能
* ログイン機能
* 勤務開始、終了機能
* 休憩開始、休憩終了機能
* 日付別勤怠情報取得
* 従業員別勤怠情報取得

## 使用技術
* PHP 8.3.0
* Laravel 8.83.27
* Laravel/fortify 1.19
* mysql 8.2.0

## ER図
![スクリーンショット  Atte ER図](https://github.com/leo-bianchini22/atte-test/assets/149698762/2e503e42-c370-4069-a5a5-24b92cbce9aa)

