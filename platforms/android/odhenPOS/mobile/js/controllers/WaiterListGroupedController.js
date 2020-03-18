function WaiterListGroupedController ($scope) {

	$scope.getCategoriesByPosition = function(cart, groupProperty) {
		var result = [];
		if(cart && cart.length) {
			var groupedCart = {};
			for (var i in cart){
				if (!groupedCart[cart[i][groupProperty]]){
					groupedCart[cart[i][groupProperty]] = [cart[i]];
				}
				else {
					groupedCart[cart[i][groupProperty]].push(cart[i]);
				}
			}
			for (var k in groupedCart){
				result.push(parseInt(k));
			}
		}
		return result.sort(function(a,b){ return a - b; }).map(function (item) { return 'posição ' + item; });
	};

    $scope.getCategoriesByGroup = function(cart, groupProperty) {
        var result = [];
        if(cart && cart.length) {
            var groupedCart = {};
            for (var i in cart){
                if (!groupedCart[cart[i][groupProperty]]){
                    groupedCart[cart[i][groupProperty]] = [cart[i]];
                }
                else {
                    groupedCart[cart[i][groupProperty]].push(cart[i]);
                }
            }
            for (var k in groupedCart){
                result.push(k);
            }
        }
        var final = result.sort(function(a,b){
            if (a > b) return 1;
            if (a < b) return -1;
            return 0;
        }).map(function (item) { return item; });
        return final;
    };

    $scope.listGroupedFieldSelect = function(row, field){
        if (!row.__isSelected){
            field.dataSource.addCheckedRows(row);
        }
        else {
            field.dataSource.removeCheckedRows(row);
        }
    };

}