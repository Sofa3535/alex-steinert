// Module.register("MMM-Discord", {
//     // Default module config.
//     defaults: {
//         userId: ""
//     },
//
//     getDiscord: function () {
//         const { Client, Intents } = require('discord.js');
//         const client = new Client({ intents: [Intents.FLAGS.GUILDS, Intents.FLAGS.GUILD_MESSAGES] });
//
//         client.on('ready', () => {
//             console.log(`Logged in as ${client.user.tag}!`);
//         });
//
//         client.on('message', msg => {
//             if (msg.content === 'ping') {
//                 msg.channel.send('pong');
//             }
//         });
//
//         client.login('token');
//     },
// });