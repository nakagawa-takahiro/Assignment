# HR クラウド 内定者インターン課題

### 9/6 の成果

- 論理削除に変更(#11)
- MVC の役割の切り分け(#10)
- bookmark の修正
- メッセージのフィルタ機能追加
- Github(#9)の解決

### よく使うファイルのリンク

[チャット画面(views/message/index.php)](/fuel/app/views/message/index.php)

[チャット保存(controller/chat.php)](/fuel/app/classes/controller/chat.php)

[チャンネル一覧画面(views/channel/channel.php)](/fuel/app/views/channel/channel.php)

[チャンネル追加(controller/channel.php)](fuel/app/classes/controller/channel.php)

<br>

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
- knockout.js 3.2.1
- ajax 3.4.1

<br>

## <要件定義>

### 開発概要

- チャットアプリ
- 単なるコミュニケーションツールではなく、ナレッジマネジメントに特化させる
- ここでいうナレッジマネジメントは、ナレッジ(メッセージ)の共有と管理を指す
- 例えば、開発時のプロジェクトに関する情報や参考文献、エラー等に対する対処法等を記録
- イメージとしては GitHub+Slack(+wiki+qiita)

### 開発要件

- ツリー構造を導入し、チャットルームをカテゴライズすることで、プロジェクトごと、トピック ごとにメッセージ(ナレッジ)を管理する

### 機能要件

- チャット機能
- good/bad 等のリアクション
- ブックマーク(メッセージを保存し、見たいときにアクセスできる)
- メッセージの編集と送信取り消し

### 開発スケジュール

- 8/23(火)から本格的に開発スタート
- 9 月第 1 週までにレビューまですすめる

### UI イメージ

![UIイメージ](./images/UI_image1.png)
※メッセージについては最低限テキストの送信だけでも実装

### [チェックシートはこちら](/main/check_sheet.md)

#### 構成

- チャンネル一覧

  → ディレクトリのような見た目

- ブックマーク

  → すぐにアクセスしたいメッセージにタイトルをつけてブックマーク

  → タイトルで検索可能

- トークページ

  → メッセージ一覧と 👍👎 とブックマーク、メッセージ編集

### 補足

-

<br>

## <DB の設計>

※詳細はこちら (今後追加：8/29)

### [データベース設計](https://docs.google.com/spreadsheets/d/1eardZKwNqOiGUFWTd6UJJSQfEZRKDcCcSSX4yI41e7E/edit?usp=sharing)
