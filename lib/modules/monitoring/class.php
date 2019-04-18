<?php
	use xPaw\SourceQuery\SourceQuery;
	class monitoring{
		
		private $eng;
		private $sq;
		private $cacheTime = 60;
		
		public function __construct(&$eng){
			$this->eng = &$eng;
			require $this->eng->homePath.'lib/SourceQuery/bootstrap.php';
			$this->sq = new SourceQuery();
		}
		
		public function getServers(){
			$servers_ = $this->eng->getServers();
			$res = [];
			for($i = 0; $i < count($servers_); $i++){
				if($servers_[$i]['active']){
					if($servers_[$i]['data']['monitorCache']['cacheTime']+$this->cacheTime < time()){
						try
						{
							$this->sq->Connect($servers_[$i]['ip'], $servers_[$i]['port'], 1, $this->getQueryMethodByName($servers_[$i]['gameData']['engine']));
							$srvData = [
								'server' => $this->sq->GetInfo(),
								'players' => $this->sq->GetPlayers(),
							];
							unset($servers_[$i]['data']['monitorCache']);
							$res[] = array_merge($srvData, ['info' => $servers_[$i]]);
							$this->setCache($servers_[$i]['id'], $srvData);
							$this->sq->Disconnect();
						}
						catch(Exception $e)
						{
							continue;
						}
					}
					else{
						$cache = json_decode($servers_[$i]['data']['monitorCache']['cache'], true);
						unset($servers_[$i]['data']['monitorCache']);
						$res[] = array_merge($cache, ['info' => $servers_[$i]]);
					}
				}
			}
			return $res;
		}
		
		private function setCache($srv, $data){
			$data_['cacheTime'] = time();
			$data_['cache'] = json_encode($data);
			$this->eng->setServerData($srv, ['monitorCache' => $data_]);
		}
		
		private function getQueryMethodByName($name){
			switch($name){
				case 'goldsource': return SourceQuery::GOLDSOURCE;
				case 'source': return SourceQuery::SOURCE;
				default: return null;
			}
		}
	}
?>