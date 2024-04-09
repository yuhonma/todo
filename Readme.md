# ToDoリスト
## 実行環境の必要条件
* php >= 8.0.1
* MySQL >= 8.0.1

## はじめに
### アプリの概要
「ToDoリスト」はやるべき作業を整理して保存するタスク管理アプリです。
### データベースの設定
MySQLでデータベースを作成し、以下のコマンドで2つのテーブルを作成してください。
```sql
CREATE TABLE task(
    id INT(11) AUTO_INCREMENT NOT NULL, 
    task VARCHAR(30) NOT NULL,
    due_time INT(11) NOT NULL,
    created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    category_id INT(11) NOT NULL,
    completed TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
);

CREATE TABLE category(
    id INT(11) AUTO_INCREMENT NOT NULL, 
    category_name VARCHAR(30) NOT NULL,
    created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
```
### データベースへの接続
library.php内の9行目にデータベース情報を入力し、接続してください。

## アプリの使い方
### タスクの追加
タスクの名前、期限(日時)、カテゴリ名を入力して追加ボタンを押します。期限とカテゴリ名の入力は任意です。
### タスク管理
* カテゴリ名をクリックするとカテゴリごとのタスクを表示することができます。
* 期限を設定したタスクは残り時間と期限の日時が表示されます。
* 終えたタスクは「タスク完了」の表示をクリックすることでそのタスクの表示は消え、ページ下部の「完了したタスク」リンクから見ることができます。
* 「編集」リンクからタスクの編集ができます。タスクを削除したい場合は「削除」の表示をクリックすることで削除することができます。
