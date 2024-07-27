
/* let token = $('meta[name="csrf-token"]').attr("content"); */

/* let hostUrl = window.location.origin;
if (hostUrl == 'https://mundoscort.com.es' || hostUrl == 'https://mundoscort.com.es/') {
    hostUrl = window.location.origin + "/login";
}

hostUrl = window.location.origin + "/login"; */


const paypalConfig = (price, button) => {
    window.paypal
        .Buttons({
            style: {
                shape: "pill",
                layout: "vertical",
                color: "blue",
                label: "buynow",
            },
            message: {
                amount: price,
            },
            async createOrder() {
                try {
                    const response = await fetch(hostUrl + "/admin/api/orders", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            'X-CSRF-TOKEN': token
                        },
                        // use the "body" param to optionally pass additional order information
                        // like product ids and quantities
                        body: JSON.stringify({
                            cart: [{
                                id: "YOUR_PRODUCT_ID",
                                quantity: "YOUR_PRODUCT_QUANTITY",
                            },],
                        }),
                    });

                    const orderData = await response.json();

                    if (orderData.id) {
                        return orderData.id;
                    }
                    const errorDetail = orderData?.details?.[0];
                    const errorMessage = errorDetail ?
                        `${errorDetail.issue} ${errorDetail.description} (${orderData.debug_id})` :
                        JSON.stringify(orderData);

                    throw new Error(errorMessage);
                } catch (error) {
                    console.error(error);
                    // resultMessage(`Could not initiate PayPal Checkout...<br><br>${error}`);
                }
            },
            async onApprove(data, actions) {
                try {
                    const response = await fetch(`${hostUrl}/admin/api/orders/${data.orderID}/capture`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            'X-CSRF-TOKEN': token
                        },
                    });

                    /* const response = await fetch(hostUrl + "/admin/api/orders/capture", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            'X-CSRF-TOKEN': token
                        },
                        // use the "body" param to optionally pass additional order information
                        // like product ids and quantities
                        body: JSON.stringify({
                            order_id: data.orderID
                        }),
                    });*/

                    const orderData = await response.json();
                    // Three cases to handle:
                    //   (1) Recoverable INSTRUMENT_DECLINED -> call actions.restart()
                    //   (2) Other non-recoverable errors -> Show a failure message
                    //   (3) Successful transaction -> Show confirmation or thank you message

                    const errorDetail = orderData?.details?.[0];

                    if (errorDetail?.issue === "INSTRUMENT_DECLINED") {
                        // (1) Recoverable INSTRUMENT_DECLINED -> call actions.restart()
                        // recoverable state, per
                        // https://developer.paypal.com/docs/checkout/standard/customize/handle-funding-failures/
                        return actions.restart();
                    } else if (errorDetail) {
                        // (2) Other non-recoverable errors -> Show a failure message
                        throw new Error(`${errorDetail.description} (${orderData.debug_id})`);
                    } else if (!orderData.purchase_units) {
                        throw new Error(JSON.stringify(orderData));
                    } else {
                        // (3) Successful transaction -> Show confirmation or thank you message
                        // Or go to another URL:  actions.redirect('thank_you.html');
                        console.log('orderData', orderData);
                        const transaction =
                            orderData?.purchase_units?.[0]?.payments?.captures?.[0] ||
                            orderData?.purchase_units?.[0]?.payments?.authorizations?.[0];
                        alert(
                            `Transaction ${transaction.status}: ${transaction.id}<br>
          <br>See console for all available details`
                        );
                        console.log(
                            "Capture result",
                            orderData,
                            JSON.stringify(orderData, null, 2)
                        );
                    }
                } catch (error) {
                    console.error(error);
                    alert(
                        `Sorry, your transaction could not be processed...<br><br>${error}`
                    );
                }
            },
        })
        .render("#paypal-button-container-" + button);
}

paypalConfig(5, 'basic');
paypalConfig(10, 'standard');
paypalConfig(20, 'unlimited');

