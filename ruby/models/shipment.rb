class Shipment
    attr_accessor :shipmentIndex, :packages, :shipTo, :returnTo, :carrier

    def initialize
        @shipmentIndex = 0
        @packages = []
    end
end
