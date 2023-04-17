class Carrier
    attr_accessor :code, :service

    def initialize(code="", service="")
        @code = code
        @service = service
    end
end
