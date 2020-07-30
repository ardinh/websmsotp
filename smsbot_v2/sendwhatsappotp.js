const Nexmo = require('nexmo')

const nexmo = new Nexmo({
  apiKey: "663fdce8",
  apiSecret: "FlRfOR0YLTIIr9ez",
  applicationId: "7075ac1e-2566-402c-9bd6-ef2faebd3015",
  privateKey: "private.key"
}, {
  apiHost: "messages-sandbox.nexmo.com"
})

nexmo.channel.send(
  { "type": "whatsapp", "number": "14157386170" },
  { "type": "whatsapp", "number": "6285950333217" },
  {
    "content": {
      "type": "text",
      "text": "This is a WhatsApp Message text message sent using the Messages API"
    }
  },
  (err, data) => {
    if (err) {
      console.error(err);
    } else {
      console.log(data.message_uuid);
    }
  }
);