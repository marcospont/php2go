<!-- LOOP var=$people name="people" item="person" -->
    <div id="person{$person.id_people}">
      <input type="checkbox" id="chk_{$person.id_people}" name="chk[]" value="{$person.id_people}"/>&nbsp;
      <a href="javascript:;" onClick="loadPerson({$person.id_people})">[Edit]</a>&nbsp;
	  <a href="javascript:;" onClick="deletePerson({$person.id_people})">[Delete]</a>
      {$person.name} - {$person.sex}
    </div>
	<script type="text/javascript">$('operations').show();</script>
<!-- ELSE LOOP -->
    <div id="empty">The are no people in the database</div>	
	<script type="text/javascript">$('operations').hide();</script>
<!-- END LOOP -->