<?php include('header.php'); ?>
			<strong style="color: green;">SUCCESS</strong><br /><br /><br />

			<?php echo $bbcode; ?><br /><br /><br />
			
			<div style="text-align: center"><strong>Form helper demo:</strong></div><br />
			
			<?php echo $this->form->open('http://www.postbin.org/p7jg0a', array('style'=>'border: 1px black solid; padding: 10px; width: 500px; margin: 0 auto;'), array('hidden_input'=>'Because we are showing off')); ?>
			
			<p><strong>Username:</strong> <?php echo $this->form->input(array(
			'type'	=> 'text',
			'name'	=> 'user',
			'id'		=> 'user',
			'value'	=> 'username'
		)); ?>
			&nbsp; &nbsp; <strong>Password:</strong> <?php echo $this->form->input(array(
			'type'	=> 'password',
			'name'	=> 'pass',
			'id'		=> 'pass',
			'value'	=> 'password'
		)); ?></p>

			<p><strong>Random select:</strong> (showing off?) <?php echo $this->form->input(array(
			'name'	=> 'test',
			'type'	=> 'select',
			'options'	=> array(
				'value_0'	=> 'Value 0',
				'value_1'	=> 'Value 1',
				'value_2'	=> array(
					'text'	=> 'Value 2',
					'selected'	=> 'selected',
				),
			),
		)); ?></p>
		
			<div style="text-align: center; margin: 0"><?php echo $this->form->input('submit'); ?></div>
			</form>
			
			<br /><br /><strong>Limit demo:</strong><br /><br />
			
			Limit to three chars: <pre><?php echo $this->text->limit('Hello world!', 'chars', 3); ?></pre>
			
			Limit to one word: <pre><?php echo $this->text->limit('Hello world!', 'words', 1); ?></pre>

<?php include('footer.php'); ?>
