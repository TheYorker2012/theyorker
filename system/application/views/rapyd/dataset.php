
  <div>

    <h2>DataSet</h2>

    <table>
    <?foreach ($items as $item):?>
      <tr>
       <td><?=$item['title']?></td><td><?=$item['body']?></td>
      </tr>
    <?endforeach;?>
    </table>
    <br />
    <?=$navigator;?>
       
  </div>
