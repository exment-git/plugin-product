# PluginInvoiceDocument - 請求書を出力してファイル列に保存します。

## 主な機能

- Hiện thị nút 請求書出力 trên màn hình chi tiết của bảng Invoice
- Click vào button để xuất ra file đính kèm ở định dạng Excel và truy cập API để tạo ra file có định dạng PDF

## 事前準備

### テンプレート導入

- サンプルテンプレートをダウンロードします。  
[サンプルテンプレート](invoice_template.zip)

- 管理者設定＞テンプレートから対象テンプレートのアップロードを行ってください。  

![サンプルテンプレート](img/image001.png)



## 実行方法

- 通知テンプレートを作成します  
※請求書をメール送信する際のテンプレートです。お好きな内容を登録してください

![実行方法](img/image005.png)

※カスタム添付ファイルに必ず<strong>${file:invoice}</strong>を設定してください

![実行方法](img/image007.png)

- 請求書テーブルに通知を作成します

![実行方法](img/image009.png)

- カスタムフォーム優先度設定を追加します

![実行方法](img/image012.png)

- 請求書テーブルにデータを作成します  
※事前に取引先を登録しておく必要があります

![実行方法](img/image014.png)

- データ詳細画面で<strong>「請求書出力」</strong>ボタンを押下します

![実行方法](img/image016.png)

- 請求書（EXCEL)が添付ファイルに追加されます  
合わせてPDFに変換したファイルが請求書（PDF）列に登録されます  
※請求書出力ボタンを押すたびに最新の情報で作成したEXCELが添付ファイルに追加されますが、PDFは常に最新のバージョンのみ保持します

![実行方法](img/image019.png)

- 「請求書送付」ボタンを押すと最新のPDFを添付した状態で取引先にメールが送信されます。


![実行方法](img/image025.png)

![実行方法](img/image028.png)


