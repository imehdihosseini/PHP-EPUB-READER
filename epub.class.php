<?php
/*
  	epub Class
	Programmer : mehdi hosseini
	Finish Date : 04 April 2012
	Last Update : 11 September 2012
	Version : 1.0.0.0
	Example :
				$test = new pages('3');
				echo $test->GetPages(); returns an array
				
				$test2 = new images('3');
				echo $test2->GetImages(); returns an array
*/


class epub{
    
public $BookLink;
protected $Count = 0;
protected $PagesArray = array();
public $RootFile = '';
protected $BookFileName;
protected $BookRoot;
protected $SubFolders;
protected $PagesName;
	
	function __construct($link){
		$this->BookLink = $link;
	}
    
    
    public function read($page){
        $temp = ( strpos($page, '-') > 0 ? '-' : ( substr($page, 1, 1) == '<' ? '<' : ( substr($page, 1, 1) == '>'  ? '>' : ( substr($page, 0, 1) == '*' ? '*' : '' ) ) ) );
        $this->SetPages();
        switch($temp){
        case '-':
            //between
            $from = explode('-', $page)[0];
            $to = explode('-', $page)[1];
            for ( $i=$from; $i <= $to;  $i++ ){
                include_once($this->BookRoot.'/'.$this->PagesName[$i]);
            }
            unset($from, $to);
            break;
        case '<':
            //X is Smaller.
            $to = str_ireplace('X<', '', $page);
            for ( $i=0; $i <= $to;  $i++ ){
                include_once($this->BookRoot.'/'.$this->PagesName[$i]);
            }
            unset($to);
            break;
        case '>':
            //X is Greater.
            $from = str_ireplace('X>', '', $page);
            for ( $i=$from; $i < sizeof($this->PagesName);  $i++ ){
                include_once($this->BookRoot.'/'.$this->PagesName[$i]);
            }
            unset($from);
            break;
        case '*':
            //Load All Pages.
            for ( $i=0; $i < sizeof($this->PagesName);  $i++ ){
                include_once($this->BookRoot.'/'.$this->PagesName[$i]);
            }
            break;
        case '':
            //Just this page.
            include_once($this->BookRoot.'/'.$this->PagesName[$page]);
        }
        unset($temp, $page);
    }
    
    /*public function write(){
           
    }
    
    private function unzip(){
        
    }
	*/
    
	function FindEpubInfoFiles($filename){
		$this->SetRoot();
		$this->BookFileName = $filename;
		//$this->FileType = $type;
		foreach($this->SetBookFolders() as $value){
			foreach($this->SetEpubInfoFiles($value) as $value2){
				$temp = $value2;
			}
		}
		return $temp;
	}
	
	function SetEpubInfoFiles($folders){
		$this->SubFolders = $folders;
		return $this->SetBookFiles();
	}
	
	function SetRoot(){
		$this->BookRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$this->BookLink;
	}
	
	function SetBookFiles(){
		$files = glob($this->SubFolders.'/'.$this->BookFileName);
		return $files;
	}
	
	function SetBookFolders()
	{
	    $data = array();
	    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->BookRoot), RecursiveIteratorIterator::SELF_FIRST);
	
	    foreach ($files as $file)
	    {
	        if (is_dir($file) === true)
	        {
	            $data[] = strval($file);
	        }
	    }
	
	    return $data;
	}
	
	function FindRootFile(){
		$_temp = $this->FindEpubInfoFiles('container.xml');
		
		$string ='';			
		$xml = simplexml_load_file($_temp);
		$namespaces = $xml->getNameSpaces(true);
		foreach($xml->rootfiles->rootfile[0]->attributes()  as $a => $b){
		if ($a == 'full-path')
		$string = $b;
		}
		$this->RootFile = $string;
	}
	
	function FindPageNo(){
		$_temp = $_temp = $this->FindEpubInfoFiles('pax.xml');
					
		$xml = simplexml_load_file($_temp);
		echo '{"pages": [';
		foreach($xml->document->page  as $item){
			$xmlno = $item->attributes();
			echo '{"no" : "'.$xmlno->no.'" , "start" : "'.$xmlno->start.'" , "end" : "'.$xmlno->end.'" , "chapter" : "'.$xmlno->chapter.'"},';
		}
		echo ']}';
	}
	
	function FilePath(){
		$this->FindRootFile();
		$_temp = str_ireplace('content.opf','',$this->RootFile);
		if($_temp != '')
		$_temp = '/'.$_temp;
		return $_temp;
	}

	//set file name to BookLink variable	
	function SetFileName(){
		$this->FindRootFile();
		$this->BookLink = $_SERVER['DOCUMENT_ROOT'].'/'.$this->BookLink.'/'.$this->RootFile;
	}
	
	function SizeOf(){
		return $this->Count;
	}
	
	//retrun result of processing in epub files.
	function get(){
	if (file_exists($this->BookLink)){
			$this->Count = 0;
			$epub = simplexml_load_file($this->BookLink);
			$namespaces = $epub->getNameSpaces(true);
			foreach($epub->manifest->item as $item){
				$xml = $item->attributes();
				if ($xml->{'media-type'} == $this->MediaType){
				array_push($this->PagesArray, $xml->href);
				$this->Count++;
				}//if
			}//foreach
			return $this->PagesArray;	
	}//if
	else
	{
		return 'File not exist';
	}		
	}//function
}//class

class pages extends epub{
	public $MediaType;
	
	//Set pages book links and return pages address.
	public function GetPages(){
		$this->MediaType = 'application/xhtml+xml';
		$this->SetFileName();
		return $this->get();	
	}
    
        public function SetPages(){
                $this->PagesName = $this->GetPages();
        }
}


class images extends epub{
	public $MediaType;
	
	//Set images book links and return pages address.
	function GetImages(){
		$this->MediaType = 'image/jpeg';
		$this->SetFileName();
		return $this->get();
	}
}
?>
