# Exports

## Excel

Mapping file:

    # From a granularity
    forEach(cells):

      sheet:

        forEach(inputSets):

          table:
            columns:
              path:
                label: Formulaire
                helper: inputPath
              field:
                label: Champ
                path: component.ref
              value:
                label: Valeur
                path: value.value.digitalValue
              uncertainty:
                label: Incertitude
                path: value.value.relativeUncertainty
            lines:
              path: inputs

Data feeding:

    $export = new ExcelExport($granularity);

    $export->addHelper('inputPath', function($input) {
        return $input->getAF()->getRef();
    });

    $export->render();
