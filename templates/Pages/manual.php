<h4>Fix database referential errors</h4>
<dl>
<dt>Add foreign key constraints, run bin/cake housekeep posts first to cleanse data</dt>
<dd>create unique index staffid on users (staffid);</dd>
<dd>ALTER TABLE posts ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT;</dd>
<dt>Remove orphan posts by pointing to staffid=1, id=1380 'vacant'</dt>
<dd>SELECT id, name FROM users WHERE staffid=1</dd>
<dd>UPDATE posts SET user_id = 1380 WHERE id IN (
    SELECT p.id FROM users u RIGHT JOIN posts p on u.id=p.user_id 
    WHERE u.staffid is NULL)
</dd>
</dl>
<h5>Some user names end with newline</h5>
<dl>
    <dt>Find them out, users and posts</dl>
    <dd>SELECT name FROM users JOIN right(name,1)='\n'</dd>
    <dd>Manually delete them (not many)</dd>
</dl>