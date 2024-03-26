import { HttpClientHeader } from "./HttpClientHeader";
import { HttpClientMethodEnum } from "./HttpClientMethodEnum";
import { HttpResponse } from "./HttpResponse";

export class HttpClient {
    public static async get(
        resource: RequestInfo,
        requestParams?: FormData|Object,
        httpHeader?: HttpClientHeader, 
    ): Promise<HttpResponse> {
        return HttpClient.createRequest(
            resource,
            HttpClientMethodEnum.GET,
            requestParams,
            httpHeader,
        );
    }

    public static async put(
        resource: RequestInfo,
        requestParams?: FormData|Object,
        httpHeader?: HttpClientHeader, 
    ): Promise<HttpResponse> {
        return HttpClient.createRequest(
            resource,
            HttpClientMethodEnum.PUT,
            requestParams,
            httpHeader,
        );
    }

    private static async createRequest(
        resource: RequestInfo, 
        method: HttpClientMethodEnum, 
        requestData?: FormData|Object,
        httpHeader?: HttpClientHeader, 
    ): Promise<HttpResponse> {
        const apiUrl = import.meta.env.VITE_API_BASE_URL;
        const isApiDebugEnabled = Boolean(import.meta.env.VITE_API_DEBUG_ENABLED);

        let path = `${apiUrl}${resource}`;
        if (isApiDebugEnabled) {
            path += `?XDEBUG_SESSION_START=PHPSTORM`
        }

        const requestInit: RequestInit = {
            method: method,
        };
        
        let headers = {
            'Content-Type': 'application/json',
        };
        if (httpHeader) {
            headers = Object.assign(headers, httpHeader);
        }
        requestInit.headers = headers;

        if (requestData) {
            requestInit.body = requestData as BodyInit;
        }
        
        const response = await fetch(path, requestInit);
        const body = await response.json();

        return {
            isOk: response.ok,
            statusCode: response.status,
            body: body,
        }
    }
}