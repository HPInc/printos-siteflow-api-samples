class Component
    attr_accessor :code, :path, :fetch, :infotech, :localFile, :preflight
    
    def initialize
        @fetch, @infotech, @localFile, @preflight = false, false, false, false
    end
end
