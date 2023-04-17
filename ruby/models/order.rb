require './models/orderdata'
require './models/destination'

class Order
    attr_accessor :destination, :orderData

    def initialize(destination="")
        @destination = Destination.new
        @destination.name = destination
        @orderData = OrderData.new
    end
end
