@extends('POS')

@section('content')
<style scoped>
    .order-container {
        width: 320px;
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
    }

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: bold;
    font-size: 14px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
    margin-bottom: 10px;
}

.order-info-btn {
    background: #f0f0f0;
    border: none;
    padding: 5px 10px;
    font-size: 12px;
    border-radius: 5px;
    cursor: pointer;
}

.order-items {
    max-height: 200px;
    overflow-y: auto;
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #ddd;
}

.item-details h4 {
    font-size: 14px;
    margin: 0;
}

.item-details p {
    font-size: 12px;
    color: gray;
    margin: 2px 0 0;
}

.item-quantity {
    display: flex;
    align-items: center;
    gap: 5px;
}

.quantity-btn {
    background: #f0f0f0;
    border: none;
    padding: 5px 10px;
    font-size: 14px;
    border-radius: 5px;
    cursor: pointer;
}

.order-summary {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #ddd;
}

.summary-item, .summary-total {
    display: flex;
    justify-content: space-between;
    margin: 5px 0;
    font-size: 14px;
}

.summary-total {
    font-weight: bold;
    font-size: 16px;
}

.payment-method {
    margin-top: 15px;
}

.payment-method p {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 8px;
}

.payment-buttons {
    display: flex;
    justify-content: space-between;
}

.payment-btn {
    background: #f0f0f0;
    border: none;
    padding: 8px 12px;
    font-size: 12px;
    border-radius: 5px;
    cursor: pointer;
    flex: 1;
    margin: 0 5px;
}

.payment-btn.active {
    background: #2f6fed;
    color: white;
}

.place-order-btn {
    width: 100%;
    background: #ff4b2b;
    color: white;
    font-size: 16px;
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 15px;

    
}


</style>
    <div class="row bg-white">
        <div class="col-md-8 border">
            <div class="mt-2">
                <div>
                    <h4>
                        <strong>
                            Chicken
                        </strong>
                    </h4>
                </div>
                <div class="row">
                    <div class="col-2">
                        <div class="border" style="padding-bottom:10px">
                            <div style="background-image: url('https://cookswithsoul.com/wp-content/uploads/2023/12/southern-fried-chicken-square-2.jpg');width:100%;height:145px;background-size: cover;background-repeat: no-repeat">
                                
                            </div>
                            <div>
                                <small>
                                    <strong>Southern Fried Chicken</strong>
                                </small>
                            </div>
                            <div>
                                ₱88.00
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="border" style="padding-bottom:10px">
                            <div style="background-image: url('https://assets.epicurious.com/photos/62f16ed5fe4be95d5a460eed/1:1/w_4318,h_4318,c_limit/RoastChicken_RECIPE_080420_37993.jpg');width:100%;height:145px;background-size: cover;background-repeat: no-repeat">
                                
                            </div>
                          
                            <div>
                                <small>
                                    <strong>Southern Fried Chicken</strong>
                                </small>
                            </div>
                            <div>
                                ₱88.00
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="border" style="padding-bottom:10px">
                            <div style="background-image: url('https://www.jocooks.com/wp-content/uploads/2019/07/garlic-and-paprika-chicken-1-14.jpg');width:100%;height:145px;background-size: cover;background-repeat: no-repeat">
                                
                            </div>
                          
                            <div>
                                <small>
                                    <strong>Garlic and Paprika</strong>
                                </small>
                            </div>
                            <div>
                                ₱88.00
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="border" style="padding-bottom:10px">
                            <div style="background-image: url('https://www.gimmesomeoven.com/wp-content/uploads/2023/04/Honey-Chicken-9.jpg');width:100%;height:145px;background-size: cover;background-repeat: no-repeat">
                                
                            </div>
                           
                            <div>
                                <small>
                                    <strong>Sticky Honey Lemon Chicken</strong>
                                </small>
                            </div>
                            <div>
                                ₱88.00
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="border" style="padding-bottom:10px">
                            <div style="background-image: url('https://www.modernhoney.com/wp-content/uploads/2023/05/Juicy-Baked-Chicken-3-cropped.jpg');width:100%;height:145px;background-size: cover;background-repeat: no-repeat">
                                
                            </div>
                            <div>
                                <small>
                                    <strong>Juicy Baked Chicken</strong>
                                </small>
                            </div>
                            <div>
                                ₱88.00
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="border" style="padding-bottom:10px">
                            <div style="background-image: url('https://www.modernhoney.com/wp-content/uploads/2023/05/Juicy-Baked-Chicken-3-cropped.jpg');width:100%;height:145px;background-size: cover;background-repeat: no-repeat">
                                
                            </div>
                            <div>
                                <small>
                                    <strong>Juicy Baked Chicken</strong>
                                </small>
                            </div>
                            <div>
                                ₱88.00
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="border" style="padding-bottom:10px">
                            <div style="background-image: url('https://www.modernhoney.com/wp-content/uploads/2023/05/Juicy-Baked-Chicken-3-cropped.jpg');width:100%;height:145px;background-size: cover;background-repeat: no-repeat">
                                
                            </div>
                            <div>
                                <small>
                                    <strong>Juicy Baked Chicken</strong>
                                </small>
                            </div>
                            <div>
                                ₱88.00
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-2">
                <div>
                    <h4>
                        <strong>
                            Burger
                        </strong>
                    </h4>
                </div>
                <div class="row">
                    <div class="col-2">
                        <div class="border" style="padding-bottom:10px">
                            <div style="background-image: url('https://www.allrecipes.com/thmb/5JVfA7MxfTUPfRerQMdF-nGKsLY=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/25473-the-perfect-basic-burger-DDMFS-4x3-56eaba3833fd4a26a82755bcd0be0c54.jpg');width:100%;height:145px;background-size: cover;background-repeat: no-repeat">
                                
                            </div>
                            <div>
                                <small>
                                    <strong>The Perfect Basic Burger</strong>
                                </small>
                            </div>
                            <div>
                                ₱88.00
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="border" style="padding-bottom:10px">
                            <div style="background-image: url('https://www.thecookierookie.com/wp-content/uploads/2023/04/featured-stovetop-burgers-recipe.jpg');width:100%;height:145px;background-size: cover;background-repeat: no-repeat">
                                
                            </div>
                          
                            <div>
                                <small>
                                    <strong>Stovetop Burgers</strong>
                                </small>
                            </div>
                            <div>
                                ₱88.00
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="border" style="padding-bottom:10px">
                            <div style="background-image: url('https://assets.epicurious.com/photos/5c745a108918ee7ab68daf79/1:1/w_2560%2Cc_limit/Smashburger-recipe-120219.jpg');width:100%;height:145px;background-size: cover;background-repeat: no-repeat">
                                
                            </div>
                          
                            <div>
                                <small>
                                    <strong>Classic Smashed Cheeseburger</strong>
                                </small>
                            </div>
                            <div>
                                ₱88.00
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="border" style="padding-bottom:10px">
                            <div style="background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcToZHaLIl_aI5Q3JsI7Zt_0CDGfs-wONIf7cw&s');width:100%;height:145px;background-size: cover;background-repeat: no-repeat">
                                
                            </div>
                           
                            <div>
                                <small>
                                    <strong>The Best Grilled</strong>
                                </small>
                            </div>
                            <div>
                                ₱88.00
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="border" style="padding-bottom:10px">
                            <div style="background-image: url('https://www.modernhoney.com/wp-content/uploads/2023/05/Juicy-Baked-Chicken-3-cropped.jpg');width:100%;height:145px;background-size: cover;background-repeat: no-repeat">
                                
                            </div>
                            <div>
                                <small>
                                    <strong>Juicy Baked Chicken</strong>
                                </small>
                            </div>
                            <div>
                                ₱88.00
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="border" style="padding-bottom:10px">
                            <div style="background-image: url('https://images.immediate.co.uk/production/volatile/sites/30/2013/05/Cheeseburger-3d7c922.jpg?resize=768,574');width:100%;height:145px;background-size: cover;background-repeat: no-repeat">
                                
                            </div>
                            <div>
                                <small>
                                    <strong>Burger Recipes</strong>
                                </small>
                            </div>
                            <div>
                                ₱88.00
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="border" style="padding-bottom:10px">
                            <div style="background-image: url('https://assets.bonappetit.com/photos/5b1981ee9731de29fe912023/1:1/w_2560%2Cc_limit/easiest-ever-grilled-veggie-burgers.jpg');width:100%;height:145px;background-size: cover;background-repeat: no-repeat">
                                
                            </div>
                            <div>
                                <small>
                                    <strong>Easiest-Ever</strong>
                                </small>
                            </div>
                            <div>
                                ₱88.00
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col border">
            <div class="order-container">
                <!-- Header -->
                <div class="order-header">
                    <span>CURRENT ORDER</span>
                    <button class="order-info-btn">
                        <i class="fas fa-info-circle"></i> ORDER INFO
                    </button>
                </div>
            
                <!-- Order Items -->
                <div class="order-items">
                    <div class="order-item">
                        <div class="item-details">
                            <h4>Schezwan Veg Burger</h4>
                            <p>₱88.00</p>
                        </div>
                        <div class="item-quantity">
                            <button class="quantity-btn">-</button>
                            <span>1</span>
                            <button class="quantity-btn">+</button>
                        </div>
                    </div>
            
                    <div class="order-item">
                        <div class="item-details">
                            <h4>Mustard Seed Veggie...</h4>
                            <p>₱195.00</p>
                        </div>
                        <div class="item-quantity">
                            <button class="quantity-btn">-</button>
                            <span>1</span>
                            <button class="quantity-btn">+</button>
                        </div>
                    </div>
            
                    <div class="order-item">
                        <div class="item-details">
                            <h4>Smash Burger Alfresco</h4>
                            <p>₱115.00</p>
                        </div>
                        <div class="item-quantity">
                            <button class="quantity-btn">-</button>
                            <span>1</span>
                            <button class="quantity-btn">+</button>
                        </div>
                    </div>
                </div>
            
                <!-- Order Summary -->
                <div class="order-summary">
                    <div class="summary-item">
                        <span>Subtotal</span>
                        <span>₱398.00</span>
                    </div>
                    <div class="summary-item">
                        <span>Tax(%)</span>
                        <span>₱0.00</span>
                    </div>
                    <div class="summary-total">
                        <span>Total</span>
                        <span>₱398.00</span>
                    </div>
                </div>
            
                <!-- Payment Method -->
                <div class="payment-method">
                    <p>Payment Method</p>
                    <div class="payment-buttons">
                        <button class="payment-btn">Cash</button>
                        <button class="payment-btn active">Debit Card</button>
                        <button class="payment-btn">E-Wallet</button>
                    </div>
                </div>
            
                <!-- Place Order Button -->
                <button class="place-order-btn">Place Order</button>
            </div>
        </div>
    </div>
@endsection