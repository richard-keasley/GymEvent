<p>Hi <?php echo $user->name;?>,</p>
<p>You recently requested a password reset from GymEvent.</p>
<p><strong>Important:</strong> If this wasn't you, please check nobody else has access to your emails. Contact Richard if you are unsure.</p>
<p>Here is your password reset key: <code><?php echo $user->reset_key;?></code></p>
<p>Please click <?php echo anchor(base_url("reset/reset/{$user->reset_key}"), 'this link to reset your password');?>. <strong>Note:</strong> This link is only valid for a short amount of time. Please do not delay!</p>
<p>Please contact Richard if you have further problems with our service.</p>