<?php $this->extend('default');
$ip = filter_input(INPUT_POST, 'ip');

$this->section('content');

$hidden = [];
$attrs = [];
echo form_open('', $attrs, $hidden); ?>
<input name="ip" value="<?php echo $ip;?>">
<input type="submit" value="OK">
<?php echo form_close();

$this->endSection();

$this->section('bottom');

$keys = ['city', 'countryCode'];
$ipinfo = new \App\Libraries\Ipinfo;
# d($ipinfo->list());
# d($ipinfo->clean());
# $ipinfo->get($ip);
echo $ipinfo->get($ip);
print_r($ipinfo->get($ip)->attributes($keys));

$this->endSection();
