// export { IFlagCategory } from './flag-category';
export interface IFlagCategory {
  id : number;
  created_at : string;
  updated_at : string;
  name : String;
  description : String;
  active : Boolean;
}

export interface IFlag {
  id : Number;
  agent_username : String;
  agent_name : String;
  lob : String;
  option_inquiry : String;
  timestamp_submitted : String;
  timestamp_acknowledged : String;
  timestamp_closed : String;
  status_info : String;
  viewed : Number;
  code_id : Number
  agent_id : Number;
  confirmed : Number;
  account : String;
  worker_id : Number;
  location : String;
  options : String;
  type : String;
  category_id : Number;
  name : String;
  has_submenu : Number;
  ref_table_submenu : String;
  cbo_title : String;
}
