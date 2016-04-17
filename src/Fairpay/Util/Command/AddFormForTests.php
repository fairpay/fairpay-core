<?php


namespace Fairpay\Util\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class AddFormForTests extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tests:add-form')
            ->setDescription('Add a form to FillFormHelper')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The form name'
            )
            ->addOption(
                'method',
                'm',
                InputArgument::OPTIONAL,
                'The method name, based on the form name by default'
            )
            ->addArgument(
                'fields',
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'The fields names'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $method = $this->toCamelCase($name, $input->getOption('method'));
        $fields = $input->getArgument('fields');

        if (!count($fields)) {
            $helper = $this->getHelper('question');
            $question = new Question('Field name: ');

            while($field = $helper->ask($input, $output, $question)) {
                $fields[] = $field;
            }
        }

        $parameters = array_map(function($parameter) {
            return "\$$parameter = null";
        }, $fields);

        $methodPhp = "class FillFormHelper extends TestCaseHelper\n{\n    public function $method(";
        $methodPhp .= implode(', ', $parameters) . ")\n    {\n        \$this->sendForm('$name', array(";

        foreach ($fields as $field) {
            $methodPhp .= "\n            '$field' => \$$field,";
        }

        $methodPhp .= "\n        ));\n    }\n";

        $FillFormHelperPhp = file_get_contents(__DIR__.'/../Tests/Helpers/FillFormHelper.php');
        $FillFormHelperPhp = preg_replace('/class FillFormHelper extends TestCaseHelper\r?\n{/', $methodPhp, $FillFormHelperPhp);
        file_put_contents(__DIR__.'/../Tests/Helpers/FillFormHelper.php', $FillFormHelperPhp);
        $output->writeln('Updated FillFormHelper.php');
    }

    private function toCamelCase($value, $default)
    {
        if ($default) {
            return $default;
        }

        return preg_replace_callback('/_(\w)/', function($m) {
            return strtoupper($m[1]);
        }, $value);
    }
}