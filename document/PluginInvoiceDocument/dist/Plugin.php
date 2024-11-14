<?php
namespace App\Plugins\PluginInvoiceDocument;

use Exceedone\Exment\Enums\FileType;
use Exceedone\Exment\Services\Plugin\PluginDocumentBase;
use Illuminate\Support\Facades\Storage;
use Exceedone\Exment\Model\File as ExmentFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\App;
use Exception;

class Plugin extends PluginDocumentBase
{
	private $is_success = false;

    /**
     * execute after creating document
     */
    protected function executed()
    {
        $document = $this->document_value;
        $file_uuid = $document->getValue('file_uuid');
        $file = ExmentFile::getData($file_uuid);
        $path = $file->path;
        $convert_excel_path = Storage::disk(config('admin.upload.disk'))->path($path);
        $custom_table_name = $this->custom_table->table_name;
        if (file_exists($convert_excel_path)) {
			$accessToken = $this->getAccessToken();
			$response = Http
				::withToken($accessToken)
				->attach(
					'file', fopen(storage_path('app/admin/' . $path), 'r'), basename($path)
				)
				->withOptions([
					'verify' => false,
				])
				->post('https://localhost/api/plugins/sampleapi/convert');
			if ($response->successful()) {
				$pathinfo = pathinfo($path);
				$unique_filename = str_replace($pathinfo['extension'], 'pdf', $pathinfo['basename']);
				$filename = str_replace($pathinfo['extension'], 'pdf', $file->filename);
				Storage::disk(config('admin.upload.disk'))->put('invoice/' . $unique_filename, $response->body());
				$exment_file = ExmentFile::saveFileInfo(FileType::CUSTOM_VALUE_COLUMN, $custom_table_name , [
					'unique_filename' => $unique_filename,
					'filename' => $filename,
				]);
				$exment_file->saveCustomValueAndColumn($this->custom_value->id, 'invoice', $this->custom_table, true);
				$this->custom_value->setValue('invoice', path_join($exment_file->local_dirname, $exment_file->local_filename));
				$this->custom_value->save();
				$this->is_success = true;
			}
        }
    }
	
	protected function getAccessToken()
	{
		$url = 'https://localhost/oauth/token';
		$params = [
			'grant_type' => 'api_key',
			'client_id' => '80505a70-6383-11ef-87da-a122dccfb241',
			'client_secret' => 'UjCvM192sjZO9SMpyFU77FU9UlSOm3HTQaFsWJWm',
			'api_key' => 'key_Y2RJdw2SKlWSnCZ9g3mhx4hLNYNtk3',
			'scope' => 'plugin',
		];
		$response = Http::asForm()->withOptions([
					'verify' => false,
				])->post($url, $params);

		if ($response->successful()) {
			$data = $response->json();
			$accessToken = $data['access_token'];
			return $accessToken;
		} else {
			$locale = App::getLocale();
			if ($locale == 'ja') {
				throw new Exception('アクセストークンの更新に失敗しました');
			} else {
				throw new Exception('Failed to refresh access token');
			}
		}
	}

    /**
    * (v3.4.3対応)画面にボタンを表示するかどうかの判定。デフォルトはtrue
    * 
    * @return bool true: 描写する false 描写しない
    */
    public function enableRender(){
        return true;
    }

	protected function getResponseMessage($result)    
    {
        if ($this->is_success) {    
            return ([    
                'result'  => true,    
                'toastr' => sprintf(exmtrans('common.message.success_execute')),    
            ]);    
        }    
        return ([    
            'result'  => false,    
            'message' => sprintf(exmtrans('common.message.error_execute')),
        ]);    
    }      
}