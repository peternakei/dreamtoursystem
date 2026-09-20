export interface Field {name:string;label:string;type:string;value:any;options:{value:string;label:string}[];required:boolean;multiple:boolean;disabled:boolean;min:string;max:string;maxlength:string;placeholder:string}
export interface Form {recordKey?:string|null;title:string;action:string;method:string;fields:Field[]}
export interface Page {module:{title:string;slug:string;showPath:string|null};records:Record<string,any>[];details:Record<string,Record<string,any>>;forms:Form[]}
