//
//  Rest.swift
//  Cook
//
//  Created by Liam Pierce on 5/28/18.
//  Copyright © 2018 Virtual Earth. All rights reserved.
//

import Foundation


class Rest{
    
    public static var timeout:Int = 10;
    public static var auth:String = "";
    
    public enum REQUEST_METHOD:String{
        case POST,GET;
    }
    
    public static func setTimeout(milliseconds m:Int){
        Rest.timeout = m;
    }
    
    public static func auth(email:String,password:String){
        Rest.post(url: "/users/auth", data: ["email":email,"password":password]){ data,response,error in
                
        }
    }
    
    public static func formRequest(_ method:REQUEST_METHOD,_ url:String, _ querydata:[String:String])->URLRequest{
        let querystring = querydata.reduce("",{$0 + "\($1.0)=\($1.1)&"});
        
        var request = URLRequest(url: URL(string: "https://cook.liampierce.us/rest\(url)")!);
        request.httpMethod = method.rawValue;
        request.httpBody = querystring.data(using: String.Encoding.utf8);
        
        return request;
    }

    public static func webRequest(_ method:REQUEST_METHOD,_ url:String,_ querydata:[String:String],callback:@escaping (Data?,URLResponse?,Error?)->Void){
        
        let task = URLSession.shared.dataTask(with: formRequest(method,url,querydata)) { data, response, error in
            DispatchQueue.main.async{
                guard ((response as! HTTPURLResponse).statusCode == 200) else{
                    print("ERROR IN REQUEST : STATUS \(((response as! HTTPURLResponse).statusCode))");
                    return
                }
                
                callback(data,response,error);
            }
        }
        
        task.resume()
    }
    
    
    public static func dataToString(_ data:Data)->String?{
        return String(data: data, encoding: String.Encoding.utf8);
    }
    
    public static func get(url:String, data: [String:String],callback: @escaping (Data?,URLResponse?,Error?)->Void){
        Rest.webRequest(REQUEST_METHOD.GET,url,data,callback: callback);
    }

    public static func post(url:String, data: [String:String],callback: @escaping (Data?,URLResponse?,Error?)->Void){
        Rest.webRequest(REQUEST_METHOD.POST,url,data,callback: callback);
    }

    
}
