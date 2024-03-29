# HR クラウド 内定者インターン課題

### [企画書](https://docs.google.com/document/d/14DnYqNwRllQmid9v-6IxLJKhYCAG9-0E_MxvIwDYjII/edit?usp=sharing)

## <課題　開発編要件>

- サーバサイド言語/フレームワークに PHP/Fuelphp を使用
- CRUD の機能が網羅されている
- フロントエンドのライブラリに knockout.js が使用されている
- ux を考慮して一部動的な ui が実装されている
- 採用一括かんりくんで使用されている stylesheet テンプレートを使用する

<br>

## <開発環境>

- MAMP
- PHP 8.0.1, 7.3
- fuelphp 1.8
- knockout.js 3.5.1
- ajax 3.4.1

<br>

## <要件定義>

### 開発概要

- Slackのようなチャットアプリ
- チャンネルを設けることでチームごと、プロジェクトごとにコミュニケーションをとることができる。

### 開発要件

- Slack のような機能を備えたチャットアプリを目指す過程で、メッセージの既読未読や鍵付きチャンネルへの招待など、細かな要素にこだわった開発にしたい。

### 機能要件

- チャット機能
- good/bad 等のリアクション
- ブックマーク(メッセージを保存し、見たいときにアクセスできる)
- メッセージの編集と送信取り消し
- メンション機能とその通知
- 既読未読の判別
- メッセージやチャンネルのフィルター検索
- 簡単なスレッド機能
- ユーザー 1 人用のプライベートチャンネル
- チャンネルに鍵をかける
- 鍵付きチャンネルへユーザーを招待
- プロフィールページ
- 2人用のDM
- 簡単な通知機能

### 開発スケジュール

- 8/23(火)から本格的に開発スタート
- 9/20(火) ごろ完成を予定


## <チェックシート>

### [チェックシート](https://docs.google.com/spreadsheets/d/1ZPREYfqALgx4OfUK6cZYECYJOt7X6JLOH_rc3lZnw50/edit?usp=sharing)

<br>

## <DB の設計>

### [データベース設計](https://docs.google.com/spreadsheets/d/1eardZKwNqOiGUFWTd6UJJSQfEZRKDcCcSSX4yI41e7E/edit?usp=sharing)

### [SQLファイル](./main/kmchat.sql)

