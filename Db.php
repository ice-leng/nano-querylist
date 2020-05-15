<?php

class Db
{
    protected $db;
    protected $tablepre;

    public function __construct()
    {
        include dirname(__DIR__) . '/config/config_global.php';

        $master = $_config['db'][1];
        $this->tablepre = $master['tablepre'];
        $pdo = new \lengbin\helper\mysql\PdoMysqlHelper();
        $this->db = $pdo->connect($master['dbhost'], $master['dbname'], $master['dbuser'], $master['dbpw']);
    }

    protected function getTable($table)
    {
        return $this->tablepre . $table;
    }

    /**
     * @return \lengbin\helper\mysql\MysqlQuery
     * @throws Exception
     */
    public function query()
    {
        $query = new \lengbin\helper\mysql\MysqlQuery();
        $query->setDb($this->db);
        return $query;
    }


    public function post($params = [])
    {
        $threadTable = $this->getTable('forum_thread');
        $postTableidTable = $this->getTable('forum_post_tableid');
        $postTable = $this->getTable('forum_post');
        $forumTable = $this->getTable('forum_forum');
        $common_member_count = $this->getTable('common_member_count');
        $typeoptionvarTable = $this->getTable('forum_typeoptionvar');

        //公共信息，使用admin用户的id
        $uid = 1;
        // 昵称
        $username = 'admin';
        //主题板块
        $forumId = 81;
        //帖子标题
        $title = 'aa2';
        //帖子内容
        $content = 'bb2';
        // 时间
        $time = time();
        try{
            //第一步：向 主题表 pre_forum_thread 中插入版块ID、用户ID、用户名、帖子标题、发帖时间等信息。
            $this->db->insert($threadTable, [
                'fid'        => $forumId,
                'authorid'   => $uid,
                'author'     => $username,
                'subject'    => $title,
                'dateline'   => $time,
                'lastpost'   => $time,
                'lastposter' => $username,
                'sortid'     => 1
            ]);
            //  第二步：获取第一步插入表 pre_forum_thread 的数据ID，作为主题ID,即 tid 
            $tid = $this->db->getLastInsertId();
            //  第三步：向 post 分表协调表 pre_forum_post_tableid 插入一条数据，这张表中只有一个自增字段 pid 
            $this->db->execute("INSERT INTO {$postTableidTable} value ()");
            //  第四步：获取 第三步 插入表 pre_forum_post_tableid 的数据ID，作为 pid 
            $pid = $this->db->getLastInsertId();
            //
            // $content (帖子内容) 图片处理  =》  [attach]1[/attach]
            // pre_forum_attachment =》  pre_forum_attachment_5

            //  第五部：向帖子表 pre_forum_post 中插入帖子相关信息，这里需要注意的是： pid为第四部的pid值，tid为第二步的tid值
            $this->db->insert($postTable, [
                'pid'      => $pid,
                'fid'      => $forumId,
                'tid'      => $tid,
                'author'   => $username,
                'authorid' => $uid,
                'subject'  => $title,
                'dateline' => $time,
                'message'  => $content,
                'first'    => 1,
            ]);
            //  第六部：更新版块 pre_forum_forum 相关主题、帖子数量信息 
            $this->db->execute("UPDATE {$forumTable} SET posts=posts+1,threads=threads+1 WHERE fid='$forumId'");
            //  第七步：更新用户 pre_common_member_count 帖子数量信息 
            $this->db->execute("UPDATE {$common_member_count} SET posts=posts+1,threads=threads+1 WHERE uid='$uid'");
            // 以上 为 正常发帖
            // 以下 为 发帖内容的 分类
            // 查找 pre_forum_typeoption 分类类型 (提示)
            // 给分类 数据 pre_forum_typeoptionvar 添加数据
            $options = [
                12 => '帮你打飞机',
                13 => '漂亮的一批',
                14 => '比较隐蔽',
                15 => '还算便宜',
                16 => '909090909009',
            ];
            $optionField = [
                'sortid',
                'tid',
                'fid',
                'optionid',
                'expiration',
                'value',
            ];
            $batchInsert = [];
            foreach ($options as $optionId => $optionContent) {
                $batchInsert[] = [
                    1,
                    $tid,
                    $forumId,
                    $optionId,
                    0,
                    $optionContent
                ];
            }
            $this->db->batchInsert($typeoptionvarTable, $optionField, $batchInsert);

        }catch (Exception $exception) {
            var_dump($exception->getMessage());
            die;
        }
    }

}
