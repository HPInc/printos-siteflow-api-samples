class Item
    attr_accessor :attributes, :components, :quantity, :shipmentIndex, 
        :printQuantity, :sourceItemId, :barcode, :sku

    def initialize
        @attributes, @components = [], []
        @quantity = 1
        @shipmentIndex = 0
    end
end
