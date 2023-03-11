export interface IAgent {
  id? : Number;
  created_at? : String;
  updated_at? : String;
  agent_name? : String;
  agent_email? : String;
  worker_id? : String;
  station_name? : String;
  location? : String;
  account? : String;
  country? : String;
  mtr_host? : String;
  mtr_highest_avg? : Number;
  mtr_highest_loss? : Number;
  mtr_result : String;
  session_id? : String;
  is_active? : Boolean;
  processing : Boolean;
  base?:  any;
  progress?: Number;
  average_latency? : Number;
  packet_loss? : Number;
  jitter? : Number;
  mos? : Number;
  download_speed? : Number;
  net_type? : String;
  Throughput_percentage? : String;
}
