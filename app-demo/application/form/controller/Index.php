<?php
namespace app\form\controller;

use bb\controller\FormController;

class Index extends FormController {

	protected $tmpl = '/static/demo/';

  	public function xxx() {
  		dump($_REQUEST);
  	}

  	public function form() {

  		$f = new \bb\form\Form();
  		$ret = $f->open('add')
  				->url(U('xxx'))
                  // ->navs(
                  //    [
                  //        ['首页', 'url', true],
                  //        ['第二页', 'url2', false]
                  //    ]
                  // )
                 ->add('str', 'name', '姓名', 111, true, true)
                 ->add(
                     'select', 'sel', "班级",
                     [
                         ['一班','1'],
                         ['二班','2', true]
                     ],
                     true,
                     true
                 )
                 ->add(
                     'checkbox', 'ckb[]', "兴趣",
                     [
                         ['足球', '1', true],
                         ['篮球', '2', true],
                         ['乒乓球', '3', true]
                     ]
                     )
                 // ->add_block('block_select', 'xxx2', ['hby', '兴趣'],
                 //     [
                 //         ['足球', '1'],
                 //         ['篮球', '2', true],
                 //         ['乒乓球', '3']
                 //     ]
                 // )
                 // ->add_block('block_checkbox', 'xxx2', ['hby', '爱好'],
                 //     [
                 //         ['足球', '1'],
                 //         ['篮球', '2', ],
                 //         ['乒乓球', '3']
                 //     ]
                 // )

                ->addMulti('hehe', [
                        ['str', 'm_name', '姓名', 'xxx'],
                        ['select', 'm_hby', '爱好', [
                            ['足球', '1'],
                            ['篮球', '2', true],
                            ['乒乓球', '3']
                        ]]
                    ],
                    [
                        ['xxx1', 2],
                        ['xxx2', 3]
                    ]
                )

                 ->submit()
                 ->close();

      return $ret;

  	}

}