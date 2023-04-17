require './models/order'

require 'httparty'

ver_2_2 = Gem::Version.new('2.2')
ver_current = Gem::Version.new(RUBY_VERSION)

if (ver_2_2 > ver_current)
    require 'digest/hmac' # Ruby version <= 2.1.10
else
    require 'openssl' # Ruby version 2.2 =>
end

class OneflowClient
    attr_accessor :endpoint, :token, :secret, :order, :retries, :retryCondition, :retryDelay

    def initialize(endpoint, token, secret, options = nil)
        @endpoint, @token, @secret = endpoint, token, secret
        set_options(options)
    end

    def set_options(options)
        @retries = (options && options.retries) ? options.retries : 3
        @retryCondition = (options && options.respond_to?("retryCondition")) ? options.method("retryCondition") : method(:isRetryableError)
        @retryDelay = (options && options.respond_to?("retryDelay")) ? options.method("retryDelay") : method(:exponentialDelay)
    end

    def create_order(destination)
        @order = Order.new(destination)
    end

    def request(method, resourcePath, data="")
        post_url = @endpoint + resourcePath
        parse_url = URI.parse(post_url)

        timestamp = Time.now.getutc
        auth_header = make_token(method, parse_url.path, timestamp)

        for attemp in 1..@retries
            case method.downcase
            when "post"
                response =  post_request(post_url, data, timestamp, auth_header)
            when "put"
                response =  put_request(post_url, data, timestamp, auth_header)
            when "get"
                response =  get_request(post_url, timestamp, auth_header)
            when "delete"
                response =  delete_request(post_url, timestamp, auth_header)
            end

            if !@retryCondition.call(response) then break end
            @retryDelay.call(response, attemp)
        end

        return response
    end

    def get_request(url, timestamp, auth_header)
        response = HTTParty.get(url, :headers => {
            'x-oneflow-date' => timestamp.to_s,
            'x-oneflow-authorization' => auth_header,
            'x-oneflow-algorithm' => 'SHA256'
        })
    end

    def post_request(url, data, timestamp, auth_header)
        response = HTTParty.post(url, :body => data, :headers => {
            'Content-Type' => 'application/json',
            'x-oneflow-date' => timestamp.to_s,
            'x-oneflow-authorization' => auth_header,
            'x-oneflow-algorithm' => 'SHA256'
        })
    end

    def put_request(url, data, timestamp, auth_header)
        response = HTTParty.put(url, :body => data, :headers => {
            'Content-Type' => 'application/json',
            'x-oneflow-date' => timestamp.to_s,
            'x-oneflow-authorization' => auth_header,
            'x-oneflow-algorithm' => 'SHA256'
        })
    end

    def delete_request(url, data, timestamp, auth_header)
        response = HTTParty.delete(url, :headers => {
            'x-oneflow-date' => timestamp.to_s,
            'x-oneflow-authorization' => auth_header,
            'x-oneflow-algorithm' => 'SHA256'
        })
    end

    def upload_request(url, local_filename)
        puts "-----------------"
        puts local_filename
        puts url
        puts "-----------------"

        # begin
        #     RestClient.put(url, file: File.new(local_filename), :headers => {'Content-Type' => 'application/pdf'})
        # rescue => e
        #     e
        # end
        # uri = URI.parse(url)
        # params = CGI::parse(uri.query)
        #https://s3-eu-west-1.amazonaws.com/dev-oneflow-files/ultraprint-539090ca61e529002b0a6386/files-in/5465cc95ed54f4796313d57c.pdf
        # puts params['Signature']
        # AWS.config(access_key_id: params['AWSAccessKeyId'])
        # s3 = AWS::S3.new
        # bucket = s3.buckets['dev-oneflow-files']
        # form = bucket.presigned_post(:key => "ultraprint-539090ca61e529002b0a6386/files-in/5465cc95ed54f4796313d57c.pdf")
        # bucket.objects["ultraprint-539090ca61e529002b0a6386/files-in/5465cc95ed54f4796313d57c.pdf"].write(:file => local_filename)

        # file = File.open(local_filename)
        # response = HTTMultiParty.put(url, :body => file, :headers => {
        #     'Content-Type' => 'application/pdf',
        #     'Content-Length' => file.size.to_s
        # })
    end

    def make_token(method, path, timestamp)
        value = method.upcase + " " + path + " " + timestamp.to_s

        ver_2_2 = Gem::Version.new('2.2')
        ver_current = Gem::Version.new(RUBY_VERSION)

        if (ver_2_2 > ver_current)
            # Ruby Version <= 2.1.10
            hmac = Digest::HMAC.new(@secret, Digest::SHA256)
            hmac.update(value)
            signature = hmac.hexdigest
        else
            # Ruby Version v2.2 =>
            signature = OpenSSL::HMAC.hexdigest(OpenSSL::Digest.new('sha256'), @secret, value)
        end

        local_authorization = @token + ":" + signature
    end

    def self.converter(object)
        if (object.instance_variables.size > 0)
            hash = {}
            object.instance_variables.each {|var| hash[var.to_s.delete("@")] = OneflowClient.converter(object.instance_variable_get(var))}
            return hash
        elsif (object.class == Array)
            array = []
            object.each {|var| array.push(OneflowClient.converter(var))}
            return array
        else
            return object
        end
    end

    def validate_order
        request("POST", "/order/validate", OneflowClient.converter(@order).to_json)
    end

    def submit_order
        request("POST", "/order", OneflowClient.converter(@order).to_json)
    end

    def upload_file(file_record, local_filename)
        upload_request(file_record["url"], local_filename)
    end

    def isRetryableError(response)
        return response.code == 429
    end

    def exponentialDelay(response, retryCount)
        coefficient = (response.code == 429) ? (0.3 * 60) : 0.1 # assume 429 limit by minute
        delay = 2.pow(retryCount) * coefficient
        randomSum = delay * 0.04 * rand(11) # 0-40% of the delay
        sleep (delay + randomSum)
    end
      
end