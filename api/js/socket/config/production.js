var config = {};

config.redis = {};
config.redis.hostname = 'redis';

config.ssl = {};
config.ssl.path = '/etc/letsencrypt/live/europasprak.com/';
config.ssl.key = 'privkey.pem';
config.ssl.certificate = 'cert.pem';
config.ssl.chain = 'fullchain.pem';

module.exports = config;
