<!-- loop var=$people name="people" item="person" -->
<!-- if $p2g.loop.people.first -->
	<script type="text/javascript">$('operations').show();</script>
<!-- end if -->
    <div id="person{$person.id_people}">
      <input type="checkbox" id="chk_{$person.id_people}" name="chk[]" value="{$person.id_people}" />&nbsp;
      <a href="javascript:;" onclick="loadPerson({$person.id_people})">[Edit]</a>&nbsp;
	  <a href="javascript:;" onclick="deletePerson({$person.id_people})">[Delete]</a>
      {$person.name} - {$person.sex}
    </div>
<!-- else loop -->
    <div id="empty">The are no people in the database</div>
	<script type="text/javascript">$('operations').hide();</script>
<!-- end loop -->