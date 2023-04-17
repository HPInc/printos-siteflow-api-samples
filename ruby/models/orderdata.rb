class OrderData
    attr_accessor :attributes, :items, :shipments, :sourceOrderId
    def initialize
        @attributes, @items, @shipments = [], [], []
    end
end