var config = {};

config.redis = {};
config.redis.hostname = 'redis';

config.ssl = {};
config.ssl.path = '/usr/local/learnintouch/letsencrypt/';
config.ssl.key = 'current-privkey.pem';
config.ssl.certificate = 'current-cert.pem';
config.ssl.chain = 'current-chain.pem';

module.exports = config;
